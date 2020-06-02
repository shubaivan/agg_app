<?php

namespace App\Services\Queue;

use App\Entity\Shop;
use App\Exception\ValidatorException;
use App\QueueModel\ResourceDataRow;
use App\Services\HandleDownloadFileData;
use App\Services\Models\BrandService;
use App\Services\Models\CategoryService;
use App\Services\Models\ProductService;
use App\Services\Models\ShopService;
use App\Util\RedisHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductDataRowHandler
{
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
     * AdtractionDataRowHandler constructor.
     * @param Logger $adtractionCsvRowHandlerLogger
     * @param ProductService $productService
     * @param BrandService $brandService
     * @param CategoryService $categoryService
     * @param ShopService $shopService
     * @param EntityManagerInterface $em
     * @param RedisHelper $redisHelper
     */
    public function __construct(
        LoggerInterface $adtractionCsvRowHandlerLogger,
        ProductService $productService,
        BrandService $brandService,
        CategoryService $categoryService,
        ShopService $shopService,
        EntityManagerInterface $em,
        RedisHelper $redisHelper
    )
    {
        $this->logger = $adtractionCsvRowHandlerLogger;
        $this->productService = $productService;
        $this->brandService = $brandService;
        $this->categoryService = $categoryService;
        $this->shopService = $shopService;
        $this->em = $em;
        $this->redisHelper = $redisHelper;
    }

    /**
     * @param ResourceDataRow $dataRow
     * @throws ValidatorException
     * @throws \Exception
     */
    public function handleCsvRow(ResourceDataRow $dataRow)
    {
        try {
            $filePath = $dataRow->getFilePath();

            $product = $this->getProductService()->createProductFromCsvRow($dataRow);
            $this->getBrandService()->createBrandFromProduct($product);

            $this->getCategoryService()->createCategoriesFromProduct($product);
            $this->getShopService()->createShopFromProduct($product);

            $this->getEm()->persist($product);

            $this->getLogger()->info('sku: ' . $product->getSku());

            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $dataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL . $filePath);

            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL . $dataRow->getShop());

            if ($dataRow->getLastProduct()) {
                $this->getRedisHelper()
                    ->hMSet(HandleDownloadFileData::TIME_SPEND_PRODUCTS_SHOP_END . $dataRow->getRedisUniqKey(),
                        [$filePath => (new \DateTime())->getTimestamp()]
                    );
            }
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
        }

        echo $dataRow->getSku() . PHP_EOL;
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
}
