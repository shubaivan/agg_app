<?php


namespace App\QueueModel;

class CarriageShop extends Queues
{
    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $shop;

    /**
     * CarriageShop constructor.
     * @param int $offset
     * @param int $limit
     * @param string $filePath
     * @param string $shop
     * @param string $redisUniqKey
     */
    public function __construct(int $offset, int $limit, string $filePath, string $shop, string $redisUniqKey)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->filePath = $filePath;
        $this->shop = $shop;
        $this->redisUniqKey = $redisUniqKey;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getShop(): string
    {
        return $this->shop;
    }
}