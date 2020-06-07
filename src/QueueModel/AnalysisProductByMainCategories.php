<?php

namespace App\QueueModel;

class AnalysisProductByMainCategories extends Queues implements LastProductInterface
{
    /**
     * @var integer
     */
    private $productId;

    /**
     * @var bool
     */
    private $lastProduct;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $shop;

    /**
     * AnalysisProductByMainCategories constructor.
     * @param int $productId
     * @param bool $lastProduct
     * @param string $filePath
     * @param string $redisUniqKey
     * @param string $shop
     */
    public function __construct(
        int $productId,
        bool $lastProduct,
        string $filePath,
        string $redisUniqKey,
        string $shop
    )
    {
        $this->redisUniqKey = $redisUniqKey;
        $this->productId = $productId;
        $this->lastProduct = $lastProduct;
        $this->filePath = $filePath;
        $this->shop = $shop;
    }


    /**
     * @return string
     */
    public function getRedisUniqKey(): string
    {
        return $this->redisUniqKey;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastProduct(): bool
    {
        return $this->lastProduct;
    }

    /**
     * {@inheritDoc}
     */
    public function setLastProduct(bool $lastProduct)
    {
        $this->lastProduct = $lastProduct;
        return $this;
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
