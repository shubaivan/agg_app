<?php

namespace App\QueueModelHandlers;

use App\Exception\ValidatorException;
use App\QueueModel\AdtractionDataRow;
use App\Services\ProductService;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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
     * AdtractionDataRowHandler constructor.
     * @param LoggerInterface $adtractionCsvRowHandlerLogger
     * @param ProductService $productService
     */
    public function __construct(LoggerInterface $adtractionCsvRowHandlerLogger, ProductService $productService)
    {
        $this->logger = $adtractionCsvRowHandlerLogger;
        $this->productService = $productService;
    }

    /**
     * @param AdtractionDataRow $adtractionDataRow
     * @throws \App\Exception\ValidatorException
     */
    public function __invoke(AdtractionDataRow $adtractionDataRow)
    {
        try {
            $product = $this->getProductService()->createProductFromAndractionCsvRow($adtractionDataRow);
            $this->getLogger()->info('sku: ' . $product->getSku());
        } catch (ValidatorException $e) {
            $this->getLogger()->error($e->getErrorsMessage());
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }

        echo $product->getSku() . PHP_EOL;
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
}
