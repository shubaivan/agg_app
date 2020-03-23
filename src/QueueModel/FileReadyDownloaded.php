<?php

namespace App\QueueModel;

class FileReadyDownloaded
{
    /**
     * @var string
     */
    private $absoluteFilePath;

    /**
     * FileReadyDownloaded constructor.
     * @param string $absoluteFilePath
     */
    public function __construct(string $absoluteFilePath)
    {
        $this->absoluteFilePath = $absoluteFilePath;
    }

    /**
     * @return string
     */
    public function getAbsoluteFilePath()
    {
        return $this->absoluteFilePath;
    }
}
