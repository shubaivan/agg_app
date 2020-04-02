<?php

namespace App\QueueModelHandlers;

use App\QueueModel\FileReadyDownloaded;
use App\Services\HandleAdtractionData;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FileReadyDownloadedHandler implements MessageHandlerInterface
{
    /**
     * @var HandleAdtractionData
     */
    private $handleAdtractionData;

    /**
     * FileReadyDownloadedHandler constructor.
     * @param HandleAdtractionData $handleAdtractionData
     */
    public function __construct(HandleAdtractionData $handleAdtractionData)
    {
        $this->handleAdtractionData = $handleAdtractionData;
    }

    /**
     * @param FileReadyDownloaded $fileReadyDownloaded
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function __invoke(FileReadyDownloaded $fileReadyDownloaded)
    {
        $this->getHandleAdtractionData()->parseCSVContent(
            $fileReadyDownloaded->getAbsoluteFilePath(),
            $fileReadyDownloaded->getShop()
        );

        echo $fileReadyDownloaded->getAbsoluteFilePath() . PHP_EOL;
    }

    /**
     * @return HandleAdtractionData
     */
    public function getHandleAdtractionData(): HandleAdtractionData
    {
        return $this->handleAdtractionData;
    }
}
