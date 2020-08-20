<?php


namespace App\QueueModel;


use App\Document\MatchSameProducts;

abstract class ResourceProductQueues implements MatchSameProducts, ResourceDataRow
{
    protected $categories = [];
    
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
     * @return string|null
     */
    public function getAttributeByName(string $name)
    {
        return $this->row[$name] ?? null;
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

    public function transform()
    {
        $this->postTransform();
    }
    
    protected function postTransform()
    {
        if (count($this->categories)) {
            $this->row['category'] = implode(' - ', array_unique($this->categories));
        }

        $this->row['identityUniqData'] = $this->generateIdentityUniqData();
    }
    
    /**
     * @return false|mixed|string|string[]|null
     */
    public function generateIdentityUniqData()
    {
        if (isset($this->row['identityUniqData']) && strlen($this->row['identityUniqData'])) {
            return $this->row['identityUniqData'];
        }

        $prepare = [];
        if ($this->getName()) {
            $prepare[] = $this->getName();
        }
        if ($this->getSku()) {
            $prepare[] = $this->getSku();
        }
        if ($this->getBrand()) {
            $prepare[] = $this->getBrand();
        }
        if ($this->getEan()) {
            $prepare[] = $this->getEan();
        }
        if ($this->getShop()) {
            $prepare[] = $this->getShop();
        }
        $implode = implode('_', $prepare);
        $preg_replace = preg_replace('!\s!', '_', $implode);

        return mb_strtolower($preg_replace);
    }
}