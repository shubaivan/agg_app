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
     * @param FileReadyDownloaded $sleepMessage
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function __invoke(FileReadyDownloaded $sleepMessage)
    {
        $seconds = $sleepMessage->getSeconds();
        $output = $sleepMessage->getOutput();
        $this->getHandleAdtractionData()->parseCSVContent($output);
        # Simulate a long running process.
//        sleep($seconds);
        echo $output . PHP_EOL;
    }

    /**
     * @return HandleAdtractionData
     */
    public function getHandleAdtractionData(): HandleAdtractionData
    {
        return $this->handleAdtractionData;
    }
}
