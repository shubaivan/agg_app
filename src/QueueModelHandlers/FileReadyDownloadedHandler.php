<?php

namespace App\QueueModelHandlers;

use App\QueueModel\FileReadyDownloaded;
use App\Services\HandleDownloadFileData;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FileReadyDownloadedHandler implements MessageHandlerInterface
{
    /**
     * @var HandleDownloadFileData
     */
    private $handleDownloadData;

    /**
     * FileReadyDownloadedHandler constructor.
     * @param HandleDownloadFileData $handleAdtractionData
     */
    public function __construct(HandleDownloadFileData $handleAdtractionData)
    {
        $this->handleDownloadData = $handleAdtractionData;
    }

    /**
     * @param FileReadyDownloaded $fileReadyDownloaded
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function __invoke(FileReadyDownloaded $fileReadyDownloaded)
    {
        $this->getHandleDownloadData()->newParseCSVContent(
            $fileReadyDownloaded->getAbsoluteFilePath(),
            $fileReadyDownloaded->getShop()
        );

        echo $fileReadyDownloaded->getAbsoluteFilePath() . PHP_EOL;
    }

    /**
     * @return HandleDownloadFileData
     */
    public function getHandleDownloadData(): HandleDownloadFileData
    {
        return $this->handleDownloadData;
    }
}
