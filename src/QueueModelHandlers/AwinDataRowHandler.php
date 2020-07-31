<?php

namespace App\QueueModelHandlers;

use App\Exception\ValidatorException;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\AwinDataRow;
use App\Services\Queue\ProductDataRowHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AwinDataRowHandler implements MessageHandlerInterface
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
     * @param AwinDataRow $row
     * @throws ValidatorException
     * @throws \Throwable
     */
    public function __invoke(AwinDataRow $row)
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
