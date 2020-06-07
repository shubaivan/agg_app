<?php

namespace App\QueueModelHandlers;

use App\Exception\ValidatorException;
use App\QueueModel\AdrecordDataRow;
use App\Services\Queue\ProductDataRowHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AdrecordDataRowHandler implements MessageHandlerInterface
{
    /**
     * @var ProductDataRowHandler
     */
    private $productDataRowHandler;

    /**
     * AdrecordDataRowHandler constructor.
     * @param ProductDataRowHandler $productDataRowHandler
     */
    public function __construct(ProductDataRowHandler $productDataRowHandler)
    {
        $this->productDataRowHandler = $productDataRowHandler;
    }

    /**
     * @param AdrecordDataRow $adrecordDataRow
     * @throws ValidatorException
     * @throws \Throwable
     */
    public function __invoke(AdrecordDataRow $adrecordDataRow)
    {
        $this->getProductDataRowHandler()->handleCsvRow($adrecordDataRow);
    }

    /**
     * @return ProductDataRowHandler
     */
    public function getProductDataRowHandler(): ProductDataRowHandler
    {
        return $this->productDataRowHandler;
    }
}
