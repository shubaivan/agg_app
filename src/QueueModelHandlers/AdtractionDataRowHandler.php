<?php

namespace App\QueueModelHandlers;

use App\Exception\ValidatorException;
use App\QueueModel\AdtractionDataRow;
use App\Services\Queue\ProductDataRowHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AdtractionDataRowHandler implements MessageHandlerInterface
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
     * @param AdtractionDataRow $adtractionDataRow
     * @throws ValidatorException
     * @throws \Exception
     */
    public function __invoke(AdtractionDataRow $adtractionDataRow)
    {
        $this->getProductDataRowHandler()->handleCsvRow($adtractionDataRow);
    }

    /**
     * @return ProductDataRowHandler
     */
    public function getProductDataRowHandler(): ProductDataRowHandler
    {
        return $this->productDataRowHandler;
    }
}
