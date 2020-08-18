<?php


namespace App\QueueModel;


use App\Document\MatchSameProducts;

abstract class ResourceProductQueues implements MatchSameProducts
{
    /**
     * @var array
     */
    protected $row;

    /**
     * @var bool
     */
    protected $lastProduct = false;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $redisUniqKey;

    /**
     * ResourceProductQueues constructor.
     * @param array $row
     * @param bool $lastProduct
     * @param string $filePath
     * @param string $redisUniqKey
     */
    public function __construct(
        array $row, 
        bool $lastProduct, 
        string $filePath, 
        string $redisUniqKey
    )
    {
        $this->row = $row;
        $this->lastProduct = $lastProduct;
        $this->filePath = $filePath;
        $this->redisUniqKey = $redisUniqKey;
    }

    /**
     * @return string
     */
    public function getRedisUniqKey(): string
    {
        return $this->redisUniqKey;
    }

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * @return string|null
     */
    public function getShop()
    {
        return $this->row['shop'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->row['SKU'] ?? null;
    }

    /**
     * @return string|null
     */
    public function setSkuValueToRow($sku)
    {
        return $this->row['SKU'] = $sku;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setExistProductId(int $id)
    {
        if ($this->getRow() && is_array($this->row)) {
            $this->row['id'] = $id;
        }

        return $this;
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
    public function getFilePath()
    {
        return $this->filePath;
    }
}