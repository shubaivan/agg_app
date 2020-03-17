<?php

# project/src/QueueModel/FileReadyDownloaded.phpded.php

namespace App\QueueModel;

class FileReadyDownloaded
{
    private $seconds;
    private $output;

    public function __construct(int $seconds, string $output)
    {
        $this->seconds = $seconds;
        $this->output = $output;
    }

    public function getSeconds()
    {
        return $this->seconds;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
