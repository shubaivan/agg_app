<?php

namespace App\QueueModelHandlers;

use App\QueueModel\CarriageShop;
use App\Services\HandleDownloadFileData;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CarriageShopHandler implements MessageHandlerInterface
{
    /**
     * @var HandleDownloadFileData
     */
    private $handleDownloadData;

    /**
     * CarriageShopHandler constructor.
     * @param HandleDownloadFileData $handleAdtractionData
     */
    public function __construct(HandleDownloadFileData $handleAdtractionData)
    {
        $this->handleDownloadData = $handleAdtractionData;
    }

    /**
     * @param CarriageShop $carriageShop
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function __invoke(CarriageShop $carriageShop)
    {
        $this->getHandleDownloadData()
            ->handleCsvByCarriage(
                $carriageShop->getOffset(),
                $carriageShop->getLimit(),
                $carriageShop->getFilePath(),
                $carriageShop->getShop(),
                $carriageShop->getRedisUniqKey()
            );
        echo 'offset ' . $carriageShop->getOffset()
            . ' limit ' . $carriageShop->getLimit()
            . ' filepath ' . $carriageShop->getFilePath()
            . PHP_EOL;
    }

    /**
     * @return HandleDownloadFileData
     */
    public function getHandleDownloadData(): HandleDownloadFileData
    {
        return $this->handleDownloadData;
    }
}
