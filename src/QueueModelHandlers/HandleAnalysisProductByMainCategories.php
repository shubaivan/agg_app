<?php

namespace App\QueueModelHandlers;

use App\Exception\ValidatorException;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\AnalysisProductByMainCategories;
use App\Services\Queue\ProductDataRowHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class HandleAnalysisProductByMainCategories implements MessageHandlerInterface
{
    /**
     * @var ProductDataRowHandler
     */
    private $productDataRowHandler;

    /**
     * HandleAnalysisProductByMainCategories constructor.
     * @param ProductDataRowHandler $productDataRowHandler
     */
    public function __construct(ProductDataRowHandler $productDataRowHandler)
    {
        $this->productDataRowHandler = $productDataRowHandler;
    }

    /**
     * @param AdrecordDataRow $adrecordDataRow
     * @throws ValidatorException
     * @throws \Exception
     */
    public function __invoke(AnalysisProductByMainCategories $analysisProductByMainCategories)
    {
        $this->getProductDataRowHandler()
            ->handleAnalysisProductByMainCategory($analysisProductByMainCategories);
    }

    /**
     * @return ProductDataRowHandler
     */
    public function getProductDataRowHandler(): ProductDataRowHandler
    {
        return $this->productDataRowHandler;
    }
}
