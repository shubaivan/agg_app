<?php

namespace App\QueueModel;

class FileReadyDownloaded extends Queues
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
     * @param string $redisUniqKey
     */
    public function __construct(
        string $absoluteFilePath, string $shop, string $redisUniqKey)
    {
        $this->absoluteFilePath = $absoluteFilePath;
        $this->shop = $shop;
        $this->redisUniqKey = $redisUniqKey;
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
