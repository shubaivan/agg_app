<?php


namespace App\QueueModel;


use App\Document\MatchSameProducts;
use App\Document\TradeDoublerProduct;

abstract class ResourceProductQueues implements MatchSameProducts, ResourceDataRow
{
    protected static $mongoClass = '';
    
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
     * @param int|string $id
     * @return $this
     */
    public function setExistProductId($id)
    {
        if ($this->getRow() && is_array($this->row)) {
            $this->row['id'] = $id;
        }

        return $this;
    }

    /**
     * @param int|string $id
     * @return $this
     */
    public function setExistMongoProductId($id)
    {
        if ($this->getRow() && is_array($this->row)) {
            $this->row['mongoId'] = $id;
        }

        return $this;
    }    
    
    public function unsetId()
    {
        if ($this->getRow() 
            && is_array($this->row)
            && isset($this->row['id'])
        ) {
            unset($this->row['id']);
        }
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
        if ((!$this->getBrand() || preg_match('/undefined/ui', $this->getBrand(), $m)) && $this->getShop()) {
            $this->row['brand'] = $this->getShop();
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

        if ($this->getSku() && strlen($this->getSku())) {
            $prepare[] = $this->getSku();
        }
        if ($this->getBrand() && strlen($this->getBrand())) {
            $prepare[] = $this->getBrand();
        }
        if ($this->getEan() && strlen($this->getEan())) {
            $prepare[] = $this->getEan();
        }
        if ($this->getShop() && strlen($this->getShop())) {
            $prepare[] = $this->getShop();
        }
        $implode = implode('_', $prepare);
        
        $preg_replace = preg_replace('/[\s+,.]+/', '_', $implode);

        return mb_strtolower($preg_replace);
    }

    /**
     * @return string
     */
    public static function getMongoClass(): string
    {
        return static::$mongoClass;
    }
}