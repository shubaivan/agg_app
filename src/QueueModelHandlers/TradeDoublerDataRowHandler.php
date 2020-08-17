<?php

namespace App\QueueModelHandlers;

use App\Exception\ValidatorException;
use App\QueueModel\TradeDoublerDataRow;
use App\Services\Queue\ProductDataRowHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TradeDoublerDataRowHandler implements MessageHandlerInterface
{
    /**
     * @var ProductDataRowHandler
     */
    private $productDataRowHandler;

    /**
     * AwinDataRowHandler constructor.
     * @param ProductDataRowHandler $productDataRowHandler
     */
    public function __construct(ProductDataRowHandler $productDataRowHandler)
    {
        $this->productDataRowHandler = $productDataRowHandler;
    }

    /**
     * @param TradeDoublerDataRow $row
     * @throws ValidatorException
     * @throws \Throwable
     */
    public function __invoke(TradeDoublerDataRow $row)
    {
        $this->getProductDataRowHandler()->handleCsvRow($row);
    }

    /**
     * @return ProductDataRowHandler
     */
    public function getProductDataRowHandler(): ProductDataRowHandler
    {
        return $this->productDataRowHandler;
    }
}
