<?php

namespace App\Services;

use App\Cache\CacheManager;
use App\Entity\Shop;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\AdtractionDataRow;
use App\QueueModel\CarriageShop;
use App\QueueModel\FileReadyDownloaded;
use App\Util\RedisHelper;
use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use Monolog\Logger;
use phpDocumentor\Reflection\Types\Self_;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;
use function League\Csv\delimiter_detect;

/**
 * Adrecord example
 * $record = {array} [17]
 * name = "OrganiCup Menskopp - Storlek A, Small"
 * category = "HUDV�RD & HYGIEN"
 * SKU = "00001-EAN000014"
 * EAN = "5711782000014"
 * description = "OrganiCup Menskopp: OrganiCup menskopp �r ett hygieniskt och �teranv�ndbart mensskydd som h�ller i m�nga �r. OrganiCup �r tillverkad i 100 % medicinskt silikon och inneh�ller inte latex, f�rg�mnen,?"
 * model = ""
 * brand = "OrganiCup"
 * price = "249.00"
 * shippingPrice = "49.00"
 * currency = "SEK"
 * productUrl = "https://click.adrecord.com/?p=935&c=37741&url=https%3A%2F%2Fwww.laplandecostore.se%2Fsv%2Farticles%2F1.448.3%2Forganicup-organicup-menskopp-storlek-a-small"
 * graphicUrl = "https://www.laplandecostore.se/images/2.6616/organicup-menskopp-storlek-a-small.png"
 * inStock = "1"
 * inStockQty = ""
 * deliveryTime = ""
 * regularPrice = "249.00"
 * gender = ""
 *
 * $record = {array} [16]
 * Adtraction example
 * SKU = "60424200882"
 * Name = "Tvådelad pyjamas med tryck grön"
 * Description = "Helmönstrad tvådelad pyjamas i mjuk ekologisk bomull. Tröjan har lång ärm med extra lång mudd vid ärmslut som går att vika upp eller ned. Och byxan har elastisk midja som går att justeras med knytband, samt extra lång mudd vid benslut.\n• Extra mjuka sömmar\n• Extra lång mudd vid ärm- och benslut<br/>Tvådelad pyjamas med tryck grön"
 * Category = "barnklader - outlet"
 * Price = "174"
 * Shipping = "39"
 * Currency = "SEK"
 * Instock = "no"
 * ProductUrl = "https://www.polarnopyret.se/barnklader/outlet/tvadelad-pyjamas-med-tryck-gron-60424200-882"
 * ImageUrl = "https://www.polarnopyret.se/globalassets/productimages-polarnopyret/7325854568173.jpg?ref=F554D52FFD"
 * TrackingUrl = "https://track.adtraction.com/t/t?a=1487384625&as=1039629367&t=2&tk=1&url=https://www.polarnopyret.se/barnklader/outlet/tvadelad-pyjamas-med-tryck-gron-60424200-882"
 * Brand = "Polarn O. Pyret"
 * OriginalPrice = "174"
 * Ean = "7325854568180"
 * ManufacturerArticleNumber = ""
 * Extras = "{SIZE#86 92}{CONDITION#new}{COLOUR#Grön}"
 *
 * Class HandleDownloadFileData
 * @package App\Services
 */
class HandleDownloadFileData
{
    const TIME_SPEND_PRODUCTS_SHOP_END = 'time_spend_products_shop_end:';
    const TIME_SPEND_PRODUCTS_SHOP_START = 'time_spend_products_shop_start:';
    const COUNT_PRODUCTS_SHOP = 'count_products_shop:';

    /**
     * @var TraceableMessageBus
     */
    private $commandBus;

    /**
     * @var TraceableMessageBus
     */
    private $productsBus;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * @var array
     */
    private $adtractionDownloadUrls;

    /**
     * @var array
     */
    private $adrecordDownloadUrls;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var int
     */
    private $csvHandleStep;

    /**
     * HandleDownloadFileData constructor.
     * @param MessageBusInterface $commandBus
     * @param MessageBusInterface $productsBus
     * @param LoggerInterface $adtractionFileHandlerLogger
     * @param RedisHelper $redisHelper
     * @param CacheManager $cacheManager
     * @param array $adtractionDownloadUrls
     * @param array $adrecordDownloadUrls
     * @param string $csvHandleStep
     */
    public function __construct(
        MessageBusInterface $commandBus,
        MessageBusInterface $productsBus,
        LoggerInterface $adtractionFileHandlerLogger,
        RedisHelper $redisHelper,
        CacheManager $cacheManager,
        array $adtractionDownloadUrls,
        array $adrecordDownloadUrls,
        string $csvHandleStep
    )
    {
        $this->adrecordDownloadUrls = $adrecordDownloadUrls;
        $this->adtractionDownloadUrls = $adtractionDownloadUrls;
        $this->commandBus = $commandBus;
        $this->productsBus = $productsBus;
        $this->logger = $adtractionFileHandlerLogger;
        $this->redisHelper = $redisHelper;
        $this->cacheManager = $cacheManager;
        $this->csvHandleStep = (int)$csvHandleStep;
    }

    /**
     * @param string $offset
     * @param string $limit
     * @param string $filePath
     * @param string $shop
     * @param string $redisUniqKey
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function handleCsvByCarriage(
        string $offset, string $limit, string $filePath, string $shop, string $redisUniqKey
    )
    {
        if (!$this->checkExistResourceWithShop($shop)) {
            $this->getLogger()->error('shop ' . $shop . ' not present on resources');
        }

        if (!file_exists($filePath)) {
            $this->getLogger()->error('file ' . $filePath . ' no exist');
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $redisUniqKey,
                    Shop::PREFIX_HANDLE_DATA_SHOP_FAILED . $filePath);
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_HANDLE_DATA_SHOP_FAILED . $shop);
            throw new \Exception('file ' . $filePath . ' no exist');
        }

        $csv = $this->generateCsvReader($filePath, $shop);

        //build a statement
        $stmt = (new Statement())
            ->offset($offset)
            ->limit($limit);

        //query your records from the document
        $records = $stmt->process($csv);
//        $header = $csv->getHeader();

        foreach ($records as $offsetRecord=>$record) {
            $this->handleProductJobInQueue(
                $shop,
                $offsetRecord,
                $record,
                $redisUniqKey,
                $filePath
            );
        }
        if ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey)) {
            unlink($filePath);
            $this->getCacheManager()->clearAllPoolsCache();
            $this->getLogger()->info(
                'file ' . $filePath . ' was removed'
            );
        }
    }

    /**
     * @param string $filePath
     * @param string|null $shop
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function creatingCarriageShop(
        string $filePath, string $shop, string $redisUniqKey)
    {
        if (!$this->checkExistResourceWithShop($shop)) {
            $this->getLogger()->error('shop ' . $shop . ' not present on resources');
        }


        if (!$this->getRedisHelper()->hExists(self::COUNT_PRODUCTS_SHOP . $redisUniqKey, $filePath)) {
            if (!file_exists($filePath)) {
                $this->getLogger()->error('file ' . $filePath . ' no exist');
                $this->getRedisHelper()
                    ->hIncrBy(Shop::PREFIX_HASH . $redisUniqKey,
                        Shop::PREFIX_HANDLE_DATA_SHOP_FAILED . $filePath);
                $this->getRedisHelper()
                    ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                        Shop::PREFIX_HANDLE_DATA_SHOP_FAILED . $shop);
                throw new \Exception('file ' . $filePath . ' no exist');
            }
        }

        $count = $this->getCount($filePath, $redisUniqKey);
        if (!$count) {
            $csv = $this->generateCsvReader($filePath, $shop);
            $count = (int)$csv->count();

            $this->getRedisHelper()
                ->hMSet(self::TIME_SPEND_PRODUCTS_SHOP_START . $redisUniqKey,
                    [$filePath => (new \DateTime())->getTimestamp()]
                );

            $this->getRedisHelper()
                ->hMSet(self::COUNT_PRODUCTS_SHOP . $redisUniqKey,
                    [$filePath => $count]
                );
        }

        for($i = 0; $i<=$count; $i = $i + $this->csvHandleStep) {
            $this->getCommandBus()->dispatch(
                new CarriageShop(
                    $i,
                    ($i + $this->csvHandleStep >= $count
                        ? $count - $i : $this->csvHandleStep),
                    $filePath,
                    $shop,
                    $redisUniqKey
                )
            );
        }
    }

    /**
     * @param string $shop
     * @return bool
     */
    private function checkExistResourceWithShop(string $shop)
    {
        if (isset($this->adtractionDownloadUrls[$shop])
            || isset($this->adrecordDownloadUrls[$shop])
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $filePath
     * @param string|null $shop
     * @return Reader
     * @throws \League\Csv\Exception
     */
    private function generateCsvReader(string $filePath, ?string $shop): Reader
    {
        /** @var Reader $csv */
        $csv = Reader::createFromPath($filePath, 'r');
        if (isset($this->adtractionDownloadUrls[$shop])) {
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(',');
            $csv->setEnclosure('\'');
        } elseif (isset($this->adrecordDownloadUrls[$shop])) {
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(';');
        }

        return $csv;
    }

    /**
     * @param string|null $shop
     * @param $offsetRecord
     * @param $record
     * @param string $redisUniqKey
     * @param string $filePath
     * @throws \Throwable
     */
    private function handleProductJobInQueue(
        ?string $shop,
        $offsetRecord,
        $record,
        string $redisUniqKey,
        string $filePath
    ): void
    {
//        echo 'shop' . $shop . ' offset ' . $offsetRecord . PHP_EOL;
        $record['shop'] = $shop;
        $this->getRedisHelper()
            ->hIncrBy(Shop::PREFIX_HASH . $redisUniqKey,
                Shop::PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL . $filePath);

        $this->getRedisHelper()
            ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                Shop::PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL . $shop);

        if (isset($this->adtractionDownloadUrls[$shop])) {
            $this->getProductsBus()->dispatch(new AdtractionDataRow(
                $record,
                $filePath,
                $redisUniqKey,
                ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey))
            ));
        }

        if (isset($this->adrecordDownloadUrls[$shop])) {
            $adrecordDataRow = new AdrecordDataRow(
                $record,
                $filePath,
                $redisUniqKey,
                ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey))
            );
            $adrecordDataRow->transform();
            $this->getProductsBus()->dispatch($adrecordDataRow);
        }
    }

    /**
     * @param string $filePath
     * @param string $date
     * @return int
     */
    private function getCount(string $filePath, string $date)
    {
        $count = (int)$this->getRedisHelper()
            ->hGet(
                self::COUNT_PRODUCTS_SHOP . $date,
                $filePath
            );

        return (int) $count;
    }


    /**
     * @return TraceableMessageBus
     */
    protected function getCommandBus()
    {
        return $this->commandBus;
    }

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return RedisHelper
     */
    protected function getRedisHelper(): RedisHelper
    {
        return $this->redisHelper;
    }

    /**
     * @return CacheManager
     */
    protected function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }

    /**
     * @return TraceableMessageBus
     */
    public function getProductsBus(): TraceableMessageBus
    {
        return $this->productsBus;
    }
}