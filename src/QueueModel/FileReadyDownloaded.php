<?php

namespace App\QueueModel;

class FileReadyDownloaded
{
    /**
     * @var string
     */
    private $absoluteFilePath;

    /**
     * @var string
     */
    private $shop;

    /**
     * FileReadyDownloaded constructor.
     * @param string $absoluteFilePath
     * @param string $shop
     */
    public function __construct(string $absoluteFilePath, string $shop)
    {
        $this->absoluteFilePath = $absoluteFilePath;
        $this->shop = $shop;
    }

    /**
     * @return string
     */
    public function getAbsoluteFilePath(): string
    {
        return $this->absoluteFilePath;
    }

    /**
     * @return string
     */
    public function getShop(): string
    {
        return $this->shop;
    }
}
