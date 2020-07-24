<?php


namespace App\QueueModel;


abstract class Queues
{
    /**
     * @var string
     */
    protected $redisUniqKey;

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