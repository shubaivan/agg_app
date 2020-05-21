<?php

namespace App\Services;

use App\Cache\CacheManager;
use App\Entity\Shop;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\AdtractionDataRow;
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
    const REDIS_ADRACTION_CARRIAGE = 'REDIS_ADRACTION_CARRIAGE';
    const REDIS_ADRECORD_CARRIAGE = 'REDIS_ARECORD_CARRIAGE';
    const HANDLE_STEP = 5;

    /**
     * @var TraceableMessageBus
     */
    private $bus;

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
     * HandleDownloadFileData constructor.
     * @param MessageBusInterface $bus
     * @param LoggerInterface $adtractionFileHandlerLogger
     * @param RedisHelper $redisHelper
     * @param CacheManager $cacheManager
     * @param array $adtractionDownloadUrls
     * @param array $adrecordDownloadUrls
     */
    public function __construct(
        MessageBusInterface $bus,
        LoggerInterface $adtractionFileHandlerLogger,
        RedisHelper $redisHelper,
        CacheManager $cacheManager,
        array $adtractionDownloadUrls,
        array $adrecordDownloadUrls
    )
    {
        $this->adrecordDownloadUrls = $adrecordDownloadUrls;
        $this->adtractionDownloadUrls = $adtractionDownloadUrls;
        $this->bus = $bus;
        $this->logger = $adtractionFileHandlerLogger;
        $this->redisHelper = $redisHelper;
        $this->cacheManager = $cacheManager;
    }


    /**
     * @param string $filePath
     * @param string|null $shop
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function parseCSVContent(string $filePath, ?string $shop)
    {
        if (!$this->checkExistResourceWithShop($shop)) {
            $this->getLogger()->error('shop ' . $shop . ' not present on resources');
        }

        $date = date("Ymd");
        if (!file_exists($filePath)) {
            $this->getLogger()->error('file ' . $filePath . ' no exist');
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $date,
                    Shop::PREFIX_HANDLE_DATA_SHOP_FAILED . $shop);
            throw new \Exception('file ' . $filePath . ' no exist');
        }
        $shopCarriage = (int)$this->getRedisHelper()
            ->hGet(
                Shop::PREFIX_HASH . $date,
                Shop::PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL . $shop
            );
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
        $count = (int)$this->getRedisHelper()
            ->hGet(
                'count_products_shop_' . $date,
                $shop
            );
        if (!$count) {
            $count = (int)$csv->count();
            $this->getRedisHelper()
                ->hMSet('count_products_shop_' . $date, [$shop => $count]);
        }

        $this->getLogger()->info(
            'file ' . $filePath . ' count row ' . $count . 'and left ' . ($count - $shopCarriage)
        );

        $offset = $shopCarriage;
        $countIteration = $shopCarriage + (int)self::HANDLE_STEP;
        while ($offset < $countIteration) {
            //build a statement
            $stmt = (new Statement())
                ->offset($offset)
                ->limit(1);

            //query your records from the document
            /** @var  $records ResultSet */
            $records = $stmt->process($csv);
            $header = $csv->getHeader();
            foreach ($records as $offsetRecord => $record) {
                $record['shop'] = $shop;
                $this->getRedisHelper()
                    ->hIncrBy(Shop::PREFIX_HASH . $date,
                        Shop::PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL . $shop);

                if (isset($this->adtractionDownloadUrls[$shop])) {
                    $this->getBus()->dispatch(new AdtractionDataRow($record));
                }

                if (isset($this->adrecordDownloadUrls[$shop])) {
                    $adrecordDataRow = new AdrecordDataRow($record);
                    $adrecordDataRow->transform();
                    $this->getBus()->dispatch($adrecordDataRow);
                }
            }

            $offset = $offsetRecord;
        }

        if ((int)$offset >= $count) {
            $this->getCacheManager()->clearAllPoolsCache();
            unlink($filePath);
        } else {
            $this->getBus()->dispatch(new FileReadyDownloaded($filePath, $shop));
        }

        $this->getLogger()->info(
            'file ' . $filePath . ' was removed'
        );
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
     * @return TraceableMessageBus
     */
    protected function getBus()
    {
        return $this->bus;
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
}