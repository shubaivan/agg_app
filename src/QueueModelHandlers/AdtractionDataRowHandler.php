<?php

namespace App\QueueModelHandlers;

use App\Exception\ValidatorException;
use App\QueueModel\AdtractionDataRow;
use App\Services\Models\BrandService;
use App\Services\Models\CategoryService;
use App\Services\Models\ProductService;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AdtractionDataRowHandler implements MessageHandlerInterface
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
     * AdtractionDataRowHandler constructor.
     * @param Logger $adtractionCsvRowHandlerLogger
     * @param ProductService $productService
     * @param BrandService $brandService
     * @param CategoryService $categoryService
     */
    public function __construct(
        LoggerInterface $adtractionCsvRowHandlerLogger,
        ProductService $productService,
        BrandService $brandService,
        CategoryService $categoryService
    )
    {
        $this->logger = $adtractionCsvRowHandlerLogger;
        $this->productService = $productService;
        $this->brandService = $brandService;
        $this->categoryService = $categoryService;
    }

    /**
     * @param AdtractionDataRow $adtractionDataRow
     * @throws ValidatorException
     * @throws \Exception
     */
    public function __invoke(AdtractionDataRow $adtractionDataRow)
    {
        try {
            $product = $this->getProductService()->createProductFromAndractionCsvRow($adtractionDataRow);
            $this->getBrandService()->createBrandFromProduct($product);
            $this->getCategoryService()->createCategoriesFromProduct($product);
            $this->getLogger()->info('sku: ' . $product->getSku());
        } catch (ValidatorException $e) {
            $this->getLogger()->error($e->getMessage());
            throw $e;
        } catch (BadRequestHttpException $e) {
            $this->getLogger()->error($e->getMessage());
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            throw $e;
        }

        echo $adtractionDataRow->getSku() . PHP_EOL;
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
    public function getCategoryService(): CategoryService
    {
        return $this->categoryService;
    }
}
