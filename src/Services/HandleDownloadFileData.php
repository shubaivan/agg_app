<?php

namespace App\Services;

use App\Cache\CacheManager;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Entity\Shop;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\AdtractionDataRow;
use App\QueueModel\AwinDataRow;
use App\QueueModel\CarriageShop;
use App\QueueModel\FileReadyDownloaded;
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

        $existProduct = $this->dm->getRepository(AwinProduct::class)
            ->findOneBy(['aw_product_id' => $awinDataRow->getSku()]);

        if (!$existProduct) {
            /**
             * @var $aw_deep_link
             * @var $product_name
             * @var $aw_product_id
             * @var $merchant_product_id
             * @var $merchant_image_url
             * @var $description
             * @var $merchant_category
             * @var $search_price
             * @var $merchant_name
             * @var $merchant_id
             * @var $category_name
             * @var $category_id
             * @var $aw_image_url
             * @var $currency
             * @var $store_price
             * @var $delivery_cost
             * @var $merchant_deep_link
             * @var $language
             * @var $last_updated
             * @var $display_price
             * @var $data_feed_id
             * @var $brand_name
             * @var $brand_id
             * @var $colour
             * @var $product_short_description
             * @var $specifications
             * @var $condition
             * @var $product_model
             * @var $model_number
             * @var $dimensions
             * @var $keywords
             * @var $promotional_text
             * @var $product_type
             * @var $commission_group
             * @var $merchant_product_category_path
             * @var $merchant_product_second_category
             * @var $merchant_product_third_category
             * @var $rrp_price
             * @var $saving
             * @var $savings_percent
             * @var $base_price
             * @var $base_price_amount
             * @var $base_price_text
             * @var $product_price_old
             * @var $delivery_restrictions
             * @var $delivery_weight
             * @var $warranty
             * @var $terms_of_contract
             * @var $delivery_time
             * @var $in_stock
             * @var $stock_quantity
             * @var $valid_from
             * @var $valid_to
             * @var $is_for_sale
             * @var $web_offer
             * @var $pre_order
             * @var $stock_status
             * @var $size_stock_status
             * @var $size_stock_amount
             * @var $merchant_thumb_url
             * @var $large_image
             * @var $alternate_image
             * @var $aw_thumb_url
             * @var $alternate_image_two
             * @var $alternate_image_three
             * @var $alternate_image_four
             * @var $ean
             * @var $isbn
             * @var $upc
             * @var $mpn
             * @var $parent_product_id
             * @var $product_GTIN
             * @var $basket_link
             * @var $Fashion_suitable_for
             * @var $Fashion_category
             * @var $Fashion_size
             * @var $Fashion_material
             * @var $Fashion_pattern
             * @var $Fashion_swatch
             */
            extract($awinDataRow->getRow());

            $adrecordProduct = new AwinProduct(
                $aw_deep_link, $product_name, $aw_product_id, $merchant_product_id,
                $merchant_image_url, $description, $merchant_category, $search_price,
                $merchant_name, $merchant_id, $category_name, $category_id, $aw_image_url,
                $currency, $store_price, $delivery_cost, $merchant_deep_link, $language,
                $last_updated, $display_price, $data_feed_id, $brand_name, $brand_id,
                $colour, $product_short_description, $specifications, $condition,
                $product_model, $model_number, $dimensions, $keywords, $promotional_text,
                $product_type, $commission_group, $merchant_product_category_path,
                $merchant_product_second_category, $merchant_product_third_category, $rrp_price,
                $saving, $savings_percent, $base_price, $base_price_amount, $base_price_text,
                $product_price_old, $delivery_restrictions, $delivery_weight, $warranty,
                $terms_of_contract, $delivery_time, $in_stock, $stock_quantity, $valid_from,
                $valid_to, $is_for_sale, $web_offer, $pre_order, $stock_status,
                $size_stock_status, $size_stock_amount, $merchant_thumb_url, $large_image,
                $alternate_image, $aw_thumb_url, $alternate_image_two, $alternate_image_three,
                $alternate_image_four, $ean, $isbn, $upc, $mpn, $parent_product_id,
                $product_GTIN, $basket_link, $Fashion_suitable_for, $Fashion_category,
                $Fashion_size, $Fashion_material, $Fashion_pattern, $Fashion_swatch, $shop
            );
            $this->dm->persist($adrecordProduct);
        };
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

        $existProduct = $this->dm->getRepository(AdtractionProduct::class)
            ->findOneBy(['SKU' => $adtractionDataRow->getSku()]);

        if (!$existProduct) {
            /**
             * @var $SKU
             * @var $Name
             * @var $Description
             * @var $Category
             * @var $Price
             * @var $Shipping
             * @var $Currency
             * @var $Instock
             * @var $ProductUrl
             * @var $ImageUrl
             * @var $TrackingUrl
             * @var $Brand
             * @var $OriginalPrice
             * @var $Ean
             * @var $ManufacturerArticleNumber
             * @var $Extras
             * @var $shop
             */
            extract($adtractionDataRow->getRow());

            $adrecordProduct = new AdtractionProduct(
                $SKU, $Name, $Description, $Category, $Price,
                $Shipping, $Currency, $Instock, $ProductUrl, $ImageUrl,
                $TrackingUrl, $Brand, $OriginalPrice, $Ean,
                $ManufacturerArticleNumber, $Extras, $shop
            );
            $this->dm->persist($adrecordProduct);
        };
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
        $existProduct = $this->dm->getRepository(AdrecordProduct::class)
            ->findOneBy(['SKU' => $adrecordDataRow->getSku()]);
        if (!$existProduct) {
            /**
             * @var $name
             * @var $category
             * @var $SKU
             * @var $EAN
             * @var $description
             * @var $model
             * @var $brand
             * @var $price
             * @var $shippingPrice
             * @var $currency
             * @var $productUrl
             * @var $graphicUrl
             * @var $inStock
             * @var $inStockQty
             * @var $deliveryTime
             * @var $regularPrice
             * @var $gender
             * @var $shop
             */
            extract($adrecordDataRow->getRow());

            $adrecordProduct = new AdrecordProduct(
                $name, $category, $SKU, $EAN, $description,
                $model, $brand, $price, $shippingPrice, $currency,
                $productUrl, $graphicUrl, $inStock, $inStockQty, $deliveryTime,
                $regularPrice, $gender, $shop
            );
            $this->dm->persist($adrecordProduct);
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