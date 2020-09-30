<?php

namespace App\Services\Queue;

use App\Cache\CacheManager;
use App\Document\AbstractDocument;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Document\TradeDoublerProduct;
use App\Entity\ManuallyResourceJob;
use App\Entity\Product;
use App\Entity\Shop;
use App\Exception\AdminShopRulesException;
use App\Exception\GlobalMatchException;
use App\Exception\GlobalMatchExceptionBrand;
use App\Exception\ValidatorException;
use App\QueueModel\ResourceDataRow;
use App\QueueModel\ResourceProductQueues;
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
     * @param ResourceProductQueues $dataRow
     * @throws ValidatorException
     * @throws \Throwable
     */
    public function handleCsvRow(ResourceProductQueues $dataRow)
    {
        $existProduct = false;
        $shouldBeRemoved = false;
        try {
            $filePath = $dataRow->getFilePath();

            $product = $this->getProductService()->createProductFromCsvRow($dataRow);
            if ($product->getId()) {
                $existProduct = true;
                $this->getProductService()->removeCustomCategoriesFromProduct($product);
            }
            $this->getCategoryService()->matchGlobalNegativeKeyWords($product);
            $this->getCategoryService()->matchGlobalNegativeBrandWords($product);
            $this->getAdminShopRulesService()->executeShopRule($product);

            $this->getBrandService()->createBrandFromProduct($product);
            $this->getCategoryService()->createCategoriesFromProduct($product);
            $this->getShopService()->createShopFromProduct($product);
            $this->getEm()->persist($product);
            $this->getEm()->flush();

            $handleAnalysisProductByMainCategory = $this->getCategoryService()
                ->handleAnalysisProductByMainCategory($product);
            if (count($handleAnalysisProductByMainCategory)) {
                $product->setMatchForCategories(true);
                $this->getEm()->flush();

                $this->getRedisHelper()
                    ->setStatisticsInRedis(
                        Shop::PREFIX_HANDLE_ANALYSIS_PRODUCT_SUCCESSFUL, $dataRow, $filePath
                    );
            }

            if (!$existProduct) {
                $this->getRedisHelper()
                    ->setStatisticsInRedis(
                        Shop::PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL_NEW_ONE, $dataRow, $filePath
                    );

            } else {
                $this->getRedisHelper()
                    ->setStatisticsInRedis(
                        Shop::PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL_EXIST, $dataRow, $filePath
                    );
            }
        } catch (AdminShopRulesException $adminShopRulesException) {
            $this->markDocumentProduct($dataRow, $adminShopRulesException);
            $shouldBeRemoved = true;
            $this->getRedisHelper()
                ->setStatisticsInRedis(
                    Shop::PREFIX_PROCESSING_DATA_SHOP_ADMIN_SHOP_RULES_EXCEPTION, $dataRow, $filePath
                );
        } catch (GlobalMatchExceptionBrand $globalMatchException) {
            $this->markDocumentProduct($dataRow, $globalMatchException);
            $shouldBeRemoved = true;
            $this->getRedisHelper()
                ->setStatisticsInRedis(
                    Shop::PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION_BRAND, $dataRow, $filePath
                );
        } catch (GlobalMatchException $globalMatchException) {
            $this->markDocumentProduct($dataRow, $globalMatchException);
            $shouldBeRemoved = true;
            $this->getRedisHelper()
                ->setStatisticsInRedis(
                    Shop::PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION, $dataRow, $filePath
                );
        } catch (ValidatorException $e) {
            $this->markDocumentProduct($dataRow, $e);
            $this->getLogger()->error($e->getMessage());

            $this->getRedisHelper()
                ->setStatisticsInRedis(
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED, $dataRow, $filePath
                );
            throw $e;
        } catch (BadRequestHttpException $e) {
            $this->getLogger()->error($e->getMessage());

            $this->getRedisHelper()
                ->setStatisticsInRedis(
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED, $dataRow, $filePath
                );
            throw $e;
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getRedisHelper()
                ->setStatisticsInRedis(
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED, $dataRow, $filePath
                );
            throw $e;
        } catch (\Throwable $exception) {
            $this->getLogger()->error($exception->getMessage());
            $this->getRedisHelper()
                ->setStatisticsInRedis(
                    Shop::PREFIX_PROCESSING_DATA_SHOP_FAILED, $dataRow, $filePath
                );
            throw $exception;
        } finally {
            if ($existProduct && $shouldBeRemoved) {
                $this->getEm()->remove($product);
                $this->getEm()->flush();
            }
            $this->postExecutedJob($dataRow);
        }

        echo $dataRow->generateIdentityUniqData() . PHP_EOL;
    }

    /**
     * @param ResourceProductQueues $dataRow
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Throwable
     */
    private function postExecutedJob(ResourceProductQueues $dataRow)
    {
        if ($dataRow->getLastProduct()) {
            $this->getCacheManager()->clearAllPoolsCache();
            /** @var ManuallyResourceJob $oneBy */
            $oneBy = $this->em->getRepository(ManuallyResourceJob::class)
                ->findOneBy(['redisUniqKey' => $dataRow->getRedisUniqKey()]);
            echo 'last_product';
            echo $oneBy ? $oneBy->getRedisUniqKey() . $oneBy->getId() : 'no';
            if ($oneBy) {
                echo 'match one by';
                $oneBy
                    ->setStatus(ManuallyResourceJob::STATUS_FINISHED);
                $this->em->flush();
            }
//                $this->getProductService()->autoVACUUM();
            $this->vacuumBus->dispatch(new VacuumJob(true));
            $this->getRedisHelper()
                ->hMSet(HandleDownloadFileData::TIME_SPEND_PRODUCTS_SHOP_END . $dataRow->getRedisUniqKey(),
                    [$dataRow->getFilePath() => (new \DateTime())->getTimestamp()]
                );
        }
    }

    /**
     * @param ResourceProductQueues $dataRow
     * @param \Exception $exception
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     * @throws \ReflectionException
     */
    private function markDocumentProduct(ResourceProductQueues $dataRow, \Exception $exception)
    {
        $documents = AbstractDocument::getChilds();
        $declineReasonClass = (new \ReflectionClass($exception))->getShortName().':'.$exception->getMessage();

        foreach ($documents as $document) {
            $oneBy = $this->dm->getRepository($document)
                ->findOneBy(['identityUniqData' => $dataRow->generateIdentityUniqData()]);
            if ($oneBy) {
                $oneBy
                    ->setDeclineReasonClass($declineReasonClass)
                    ->setDecline(true);
                $this->dm->flush(array('safe'=>true));
                break;
            }
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
