<?php

namespace App\QueueModel;

class AdtractionDataRow extends Queues implements ResourceDataRow, LastProductInterface
{
    /**
     * @var array
     */
    private $row;

    /**
     * @var bool
     */
    private $lastProduct;

    /**
     * @var string
     */
    private $filePath;

    /**
     * AdtractionDataRow constructor.
     * @param array $row
     * @param string $filePath
     * @param bool $lastProduct
     */
    public function __construct(
        array $row,
        string $filePath,
        string $redisUniqKey,
        bool $lastProduct = false
    ) {
        $this->row = $row;
        $this->lastProduct = $lastProduct;
        $this->redisUniqKey = $redisUniqKey;
        $this->filePath = $filePath;
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
    public function setSku(string $sku)
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
