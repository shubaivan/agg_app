<?php

namespace App\Services;

use App\Cache\CacheManager;
use App\Document\AbstractDocument;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Document\TradeDoublerProduct;
use App\DocumentRepository\AdrecordProductRepository;
use App\DocumentRepository\AdtractionProductRepository;
use App\DocumentRepository\AwinProductRepository;
use App\DocumentRepository\CarefulSavingSku;
use App\DocumentRepository\TradeDoublerProductRepository;
use App\Entity\Product;
use App\Entity\Shop;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\AdtractionDataRow;
use App\QueueModel\AwinDataRow;
use App\QueueModel\CarriageShop;
use App\QueueModel\FileReadyDownloaded;
use App\QueueModel\ResourceDataRow;
use App\QueueModel\ResourceProductQueues;
use App\QueueModel\TradeDoublerDataRow;
use App\Services\Models\CategoryService;
use App\Util\RedisHelper;
use Doctrine\ODM\MongoDB\DocumentManager;
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
     * @var
     */
    private $awinDownloadUrls;

    /**
     * @var
     */
    private $tradedoublerDownloadUrls;

    /**
     * @var int
     */
    private $csvHandleStep;

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var Helpers
     */
    private $helper;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * HandleDownloadFileData constructor.
     * @param MessageBusInterface $commandBus
     * @param MessageBusInterface $productsBus
     * @param LoggerInterface $adtractionFileHandlerLogger
     * @param RedisHelper $redisHelper
     * @param array $adtractionDownloadUrls
     * @param array $adrecordDownloadUrls
     * @param array $awinDownloadUrls
     * @param array $tradedoublerDownloadUrls
     * @param string $csvHandleStep
     * @param CategoryService $categoryService
     * @param Helpers $helpers
     * @param DocumentManager $dm
     */
    public function __construct(
        MessageBusInterface $commandBus,
        MessageBusInterface $productsBus,
        LoggerInterface $adtractionFileHandlerLogger,
        RedisHelper $redisHelper,
        array $adtractionDownloadUrls,
        array $adrecordDownloadUrls,
        array $awinDownloadUrls,
        array $tradedoublerDownloadUrls,
        string $csvHandleStep,
        CategoryService $categoryService,
        Helpers $helpers,
        DocumentManager $dm
    )
    {
        $this->dm = $dm;
        $this->awinDownloadUrls = $awinDownloadUrls;
        $this->adrecordDownloadUrls = $adrecordDownloadUrls;
        $this->adtractionDownloadUrls = $adtractionDownloadUrls;
        $this->tradedoublerDownloadUrls = $tradedoublerDownloadUrls;
        $this->commandBus = $commandBus;
        $this->productsBus = $productsBus;
        $this->logger = $adtractionFileHandlerLogger;
        $this->redisHelper = $redisHelper;
        $this->csvHandleStep = (int)$csvHandleStep;
        $this->categoryService = $categoryService;
        $this->helper = $helpers;
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
        try {
            if (!$this->checkExistResourceWithShop($shop)) {
                $this->getLogger()->error('shop ' . $shop . ' not present on resources');
                throw new \Exception('shop ' . $shop . ' not present on resources');
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
//            $header = $csv->getHeader();

            foreach ($records as $offsetRecord => $record) {
                if ($offsetRecord == 0) {
                    continue;
                }
                $this->handleProductJobInQueue(
                    $shop,
                    $offsetRecord,
                    $record,
                    $redisUniqKey,
                    $filePath
                );
            }
            $this->dm->flush(array('safe'=>true));
            
            if ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey)) {
                //ToDo don't forget rerun back
                unlink($filePath);
                $this->getLogger()->info(
                    'file ' . $filePath . ' was removed'
                );
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            throw $e;
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
        if (!(int)$count) {
            return;
        }
        for ($i = 0; $i <= $count; $i = $i + $this->csvHandleStep) {
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
            || isset($this->awinDownloadUrls[$shop])
            || isset($this->tradedoublerDownloadUrls[$shop])
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
        $csv->setHeaderOffset(0);
        if (isset($this->adtractionDownloadUrls[$shop])) {
            $csv->setDelimiter(',');
            $csv->setEscape('"');
            $csv->setEnclosure('\'');
        } elseif (isset($this->awinDownloadUrls[$shop])) {
            $csv->setDelimiter(',');
        } elseif (isset($this->adrecordDownloadUrls[$shop])) {
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
        $record['shop'] = Shop::getRealShopNameByKey($shop);
        $this->getRedisHelper()
            ->hIncrBy(Shop::PREFIX_HASH . $redisUniqKey,
                Shop::PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL . $filePath);

        $this->getRedisHelper()
            ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                Shop::PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL . $shop);

        if (isset($this->awinDownloadUrls[$shop])) {
            $this->createAwinJob(
                $shop,
                $offsetRecord,
                $record,
                $redisUniqKey,
                $filePath
            );
        }

        if (isset($this->tradedoublerDownloadUrls[$shop])) {
            $this->createTradeDoublerJob(
                $shop,
                $offsetRecord,
                $record,
                $redisUniqKey,
                $filePath
            );
        }
        
        if (isset($this->adtractionDownloadUrls[$shop])) {
            $this->createAdtractionDataJob(
                $shop, 
                $offsetRecord, 
                $record,
                $redisUniqKey,
                $filePath
            );
        }

        if (isset($this->adrecordDownloadUrls[$shop])) {
            $this->createAdrecordJob(
                $shop,
                $offsetRecord,
                $record,
                $redisUniqKey,
                $filePath
            );
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

        return (int)$count;
    }

    /**
     * @param string|null $shop
     * @param $offsetRecord
     * @param $record
     * @param string $redisUniqKey
     * @param string $filePath
     *
     * @return array
     * @throws \Throwable
     */
    private function createTradeDoublerJob(
        ?string $shop,
        $offsetRecord,
        $record,
        string $redisUniqKey,
        string $filePath
    ) :void
    {
        $tradeDoublerDataRow = new TradeDoublerDataRow(
            $record,
            ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey)),
            $filePath,
            $redisUniqKey
        );
        $tradeDoublerDataRow->transform();
        $this->getProductsBus()->dispatch($tradeDoublerDataRow);


        /** @var TradeDoublerProductRepository $savingSku */
        $savingSku = $this->dm->getRepository(TradeDoublerProduct::class);
        $this->saveProductInMongo(
            $tradeDoublerDataRow,
            $shop,
            $savingSku
        );
    }
    
    /**
     * @param string|null $shop
     * @param $offsetRecord
     * @param $record
     * @param string $redisUniqKey
     * @param string $filePath
     *
     * @return array
     * @throws \Throwable
     */
    private function createAwinJob(
        ?string $shop,
        $offsetRecord,
        $record,
        string $redisUniqKey,
        string $filePath
    ) :void
    {
        $awinDataRow = new AwinDataRow(
            $record,
            ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey)),
            $filePath,
            $redisUniqKey
        );
        $awinDataRow->transform();
        $this->getProductsBus()->dispatch($awinDataRow);


        /** @var AwinProductRepository $savingSku */
        $savingSku = $this->dm->getRepository(AwinProduct::class);
        $this->saveProductInMongo(
            $awinDataRow,
            $shop,
            $savingSku
        );
    }
    
    /**
     * @param string|null $shop
     * @param $offsetRecord
     * @param $record
     * @param string $redisUniqKey
     * @param string $filePath
     *
     * @throws \Throwable
     */
    private function createAdtractionDataJob(
        ?string $shop, 
        $offsetRecord,
        $record,
        string $redisUniqKey,
        string $filePath
    ) :void
    {
        $adtractionDataRow = new AdtractionDataRow(
            $record,
            ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey)),
            $filePath,
            $redisUniqKey
        );
        $this->getProductsBus()->dispatch($adtractionDataRow);
        /** @var AdtractionProductRepository $savingSku */
        $savingSku = $this->dm->getRepository(AdtractionProduct::class);
        $this->saveProductInMongo(
            $adtractionDataRow, 
            $shop,
            $savingSku
        );
    }

    /**
     * @param string|null $shop
     * @param $offsetRecord
     * @param $record
     * @param string $redisUniqKey
     * @param string $filePath
     * 
     * @throws \Throwable
     */
    private function createAdrecordJob(
        ?string $shop,
        $offsetRecord, 
        $record,
        string $redisUniqKey,
        string $filePath): void
    {
        $adrecordDataRow = new AdrecordDataRow(
            $record,
            ((int)$offsetRecord >= $this->getCount($filePath, $redisUniqKey)),
            $filePath,
            $redisUniqKey
        );
        $adrecordDataRow->transform();
        $this->getProductsBus()->dispatch($adrecordDataRow);

        /** @var AdrecordProductRepository $savingSku */
        $savingSku = $this->dm->getRepository(AdrecordProduct::class);
        $this->saveProductInMongo(
            $adrecordDataRow,
            $shop,
            $savingSku
        );
    }

    /**
     * @param ResourceProductQueues $productQueues
     * @param $shop
     * @param CarefulSavingSku $savingSku
     */
    private function saveProductInMongo(
        ResourceProductQueues $productQueues,
        $shop,
        CarefulSavingSku $savingSku
    )
    {
        /** @var AdtractionProduct $productMatch */
        $productMatch = $savingSku
            ->matchExistProduct($productQueues->getSku());

        if ($productMatch && $productMatch->getId()) {
            if ($productMatch->getShop() != $productQueues->getShop()) {
                $this->modifyToUniqSku($productQueues, $savingSku);
            } else {
                return;
            }
        }

        $savingSku->createProduct($productQueues, $shop);
    }

    /**
     * @param ResourceProductQueues $adtractionDataRow
     * @param CarefulSavingSku $savingSku
     */
    private function modifyToUniqSku(
        ResourceProductQueues $adtractionDataRow,
        CarefulSavingSku $savingSku
    )
    {
        $modifySku = $adtractionDataRow->getSku() . '_1';
        
        $matchSku = $savingSku
            ->matchExistProduct($modifySku);
        $adtractionDataRow->setSkuValueToRow($modifySku);
        while ($matchSku instanceof AbstractDocument) {
            if ($matchSku->getShop() == $adtractionDataRow->getShop()) {
                break;
            }
            $modifySku = $modifySku . '1';
            $matchSku = $savingSku
                ->matchExistProduct($modifySku);
            $adtractionDataRow->setSkuValueToRow($modifySku);
        }
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
     * @return TraceableMessageBus
     */
    public function getProductsBus(): TraceableMessageBus
    {
        return $this->productsBus;
    }
}