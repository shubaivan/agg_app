<?php

namespace App\Services\Queue;

use App\Cache\CacheManager;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Entity\Product;
use App\Entity\Shop;
use App\Exception\AdminShopRulesException;
use App\Exception\GlobalMatchException;
use App\Exception\GlobalMatchExceptionBrand;
use App\Exception\ValidatorException;
use App\QueueModel\ResourceDataRow;
use App\QueueModel\VacuumJob;
use App\Services\HandleDownloadFileData;
use App\Services\Models\AdminShopRulesService;
use App\Services\Models\BrandService;
use App\Services\Models\CategoryService;
use App\Services\Models\ProductService;
use App\Services\Models\ShopService;
use App\Util\RedisHelper;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;

class ProductDataRowHandler
{
    /**
     * @var TraceableMessageBus
     */
    private $vacuumBus;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var BrandService
     */
    private $brandService;

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var ShopService
     */
    private $shopService;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * @var string
     */
    private $forceAnalysis;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var AdminShopRulesService
     */
    private $adminShopRulesService;

    /**
     * @var DocumentManager
     */
    private $dm;


    /**
     * AdtractionDataRowHandler constructor.
     * @param MessageBusInterface $vacuumBus
     * @param Logger $adtractionCsvRowHandlerLogger
     * @param ProductService $productService
     * @param BrandService $brandService
     * @param CategoryService $categoryService
     * @param ShopService $shopService
     * @param EntityManagerInterface $em
     * @param RedisHelper $redisHelper
     * @param AdminShopRulesService $adminShopRulesService
     * @param DocumentManager $dm
     */
    public function __construct(
        MessageBusInterface $vacuumBus,
        LoggerInterface $adtractionCsvRowHandlerLogger,
        ProductService $productService,
        BrandService $brandService,
        CategoryService $categoryService,
        ShopService $shopService,
        EntityManagerInterface $em,
        RedisHelper $redisHelper,
        CacheManager $cacheManager,
        string $forceAnalysis,
        AdminShopRulesService $adminShopRulesService,
        DocumentManager $dm
    )
    {
        $this->cacheManager = $cacheManager;
        $this->vacuumBus = $vacuumBus;
        $this->logger = $adtractionCsvRowHandlerLogger;
        $this->productService = $productService;
        $this->brandService = $brandService;
        $this->categoryService = $categoryService;
        $this->shopService = $shopService;
        $this->em = $em;
        $this->redisHelper = $redisHelper;
        $this->forceAnalysis = $forceAnalysis;
        $this->adminShopRulesService = $adminShopRulesService;
        $this->dm = $dm;
    }

    /**
     * @param ResourceDataRow $dataRow
     * @throws ValidatorException
     * @throws \Throwable
     */
    public function handleCsvRow(ResourceDataRow $dataRow)
    {
        try {
            $filePath = $dataRow->getFilePath();

            $product = $this->getProductService()->createProductFromCsvRow($dataRow);

            $this->getAdminShopRulesService()->executeShopRule($product);
//            $this->getCategoryService()->matchGlobalNegativeKeyWords($product);
            $this->getCategoryService()->matchGlobalNegativeBrandWords($product);

            $this->getBrandService()->createBrandFromProduct($product);
            $this->getCategoryService()->createCategoriesFromProduct($product);
            $this->getShopService()->createShopFromProduct($product);
            if (!$product->isMatchForCategories() || $this->forceAnalysis == '1') {
                $handleAnalysisProductByMainCategory = $this->getCategoryService()
                    ->handleAnalysisProductByMainCategory($product);
                if (count($handleAnalysisProductByMainCategory)) {
                    $product->setMatchForCategories(true);

                    $this->getRedisHelper()
                        ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                            Shop::PREFIX_HANDLE_ANALYSIS_PRODUCT_SUCCESSFUL . $filePath);
                    $this->getRedisHelper()
                        ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                            Shop::PREFIX_HANDLE_ANALYSIS_PRODUCT_SUCCESSFUL . $dataRow->getShop());
                }
            }

            $this->getEm()->persist($product);

            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL . $filePath);

            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL . $dataRow->getShop());

            if ($dataRow->getLastProduct()) {
                $this->getCacheManager()->clearAllPoolsCache();
//                $this->getProductService()->autoVACUUM();
                $this->vacuumBus->dispatch(new VacuumJob(true));
                $this->getRedisHelper()
                    ->hMSet(HandleDownloadFileData::TIME_SPEND_PRODUCTS_SHOP_END . $dataRow->getRedisUniqKey(),
                        [$filePath => (new \DateTime())->getTimestamp()]
                    );
            }
        } catch (AdminShopRulesException $adminShopRulesException) {
            $this->markDocumentProduct($dataRow, $adminShopRulesException);
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_ADMIN_SHOP_RULES_EXCEPTION . $dataRow->getShop());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_ADMIN_SHOP_RULES_EXCEPTION . $filePath);
        } catch (GlobalMatchExceptionBrand $globalMatchException) {
            $this->markDocumentProduct($dataRow, $globalMatchException);
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION_BRAND . $dataRow->getShop());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION_BRAND . $filePath);
        } catch (GlobalMatchException $globalMatchException) {
            $this->markDocumentProduct($dataRow, $globalMatchException);
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION . $dataRow->getShop());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION . $filePath);
        } catch (ValidatorException $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $filePath);
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $dataRow->getShop());
            throw $e;
        } catch (BadRequestHttpException $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $dataRow->getShop());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $filePath);
            throw $e;
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $dataRow->getShop());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $filePath);
            throw $e;
        } catch (\Throwable $exception) {
            $this->getLogger()->error($exception->getMessage());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $dataRow->getShop());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED . $filePath);
            throw $exception;
        }

        echo $dataRow->getSku() . PHP_EOL;
    }

    /**
     * @param ResourceDataRow $dataRow
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function markDocumentProduct(ResourceDataRow $dataRow, \Exception $exception)
    {
        /** @var AdrecordProduct $adrecordDoc */
        $adrecordDoc = $this->dm->getRepository(AdrecordProduct::class)
            ->findOneBy(['SKU' => $dataRow->getSku()]);
        
        if ($adrecordDoc) {
            $adrecordDoc
                ->setDeclineReasonClass(get_class($exception) . ':' . $exception->getMessage())
                ->setDecline(true);
        }
        /** @var AdtractionProduct $adtractionDoc */
        $adtractionDoc = $this->dm->getRepository(AdtractionProduct::class)
            ->findOneBy(['SKU' => $dataRow->getSku()]);
        
        if ($adtractionDoc) {
            $adtractionDoc
                ->setDeclineReasonClass(get_class($exception) . ':' . $exception->getMessage())
                ->setDecline(true);
        }
        if ($adrecordDoc || $adtractionDoc) {
            $this->dm->flush();
        }
    }

    /**
     * @return ProductService
     */
    protected function getProductService(): ProductService
    {
        return $this->productService;
    }

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return BrandService
     */
    protected function getBrandService(): BrandService
    {
        return $this->brandService;
    }

    /**
     * @return CategoryService
     */
    protected function getCategoryService(): CategoryService
    {
        return $this->categoryService;
    }

    /**
     * @return ShopService
     */
    protected function getShopService(): ShopService
    {
        return $this->shopService;
    }

    /**
     * @return EntityManager
     */
    protected function getEm(): EntityManager
    {
        return $this->em;
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
    private function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }

    /**
     * @return AdminShopRulesService
     */
    private function getAdminShopRulesService(): AdminShopRulesService
    {
        return $this->adminShopRulesService;
    }
}
