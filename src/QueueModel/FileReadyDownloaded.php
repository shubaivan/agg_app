<?php

namespace App\QueueModel;

class FileReadyDownloaded
{
    private $absoluteFilePath;

    /**
     * FileReadyDownloaded constructor.
     * @param $output
     */
    public function __construct($output)
    {
        $this->absoluteFilePath = $output;
    }

    /**
     * @return mixed
     */
    public function getAbsoluteFilePath()
    {
        return $this->absoluteFilePath;
    }
}
