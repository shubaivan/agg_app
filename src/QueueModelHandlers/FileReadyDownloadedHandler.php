<?php

# project/src/QueueModelHandlers/FileReadyDownloadedHandler.phpler.php

namespace App\QueueModelHandlers;

use App\QueueModel\FileReadyDownloaded;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FileReadyDownloadedHandler implements MessageHandlerInterface
{
    public function __invoke(FileReadyDownloaded $sleepMessage)
    {
        $seconds = $sleepMessage->getSeconds();
        $output = $sleepMessage->getOutput();

        # Simulate a long running process.
        sleep($seconds);
        echo $output . PHP_EOL;
    }
}
