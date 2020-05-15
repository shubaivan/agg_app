<?php

namespace App\Entity\Collection\SearchProducts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\ConversionException;
use JMS\Serializer\Annotation;
use App\Entity\Collection\SearchProductCollection;

class GroupProductEntity
{
    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreSkuAccessor")
     */
    private $storeSku;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreCreatedAtAccessor")
     */
    private $storeCreatedAt;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreNamesAccessor")
     */
    private $storeNames;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreDescriptionAccessor")
     */
    private $storeDescription;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreExtrasAccessor")
     */
    private $storeExtras;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStorePriceAccessor")
     */
    private $storePrice;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreImageUrlAccessor")
     */
    private $storeImageUrl;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreProductUrlAccessor")
     */
    private $storeProductUrl;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreNumberOfEntriesAccessor")
     */
    private $storeNumberOfEntries;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setCreatedAtAccessor")
     */
    private $createdAt;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setIdsAccessor")
     */
    private $ids;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setNamesAccessor")
     */
    private $names;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setDescriptionAccessor")
     */
    private $description;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setExtrasAccessor")
     */
    private $extras;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setPriceAccessor")
     */
    private $price;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setNumberOfEntriesAccessor")
     */
    private $numberOfEntries;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setShopAccessor")
     */
    private $shop;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setShopRelationIdAccessor")
     */
    private $shopRelationId;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setBrandAccessor")
     */
    private $brand;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setCurrencyAccessor")
     */
    private $currency;

    /**
     * @var ArrayCollection|AdjacentProduct[]
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    private $adjacentProducts = [];

    /**
     * @var AdjacentProduct
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    private $currentProduct;

    /**
     * @var array
     */
    private $presentAdjacentProducts = [];

    /**
     * @var array
     */
    private $presentCurrentProduct = [];

    /**
     * @param string $value
     * @throws ConversionException
     */
    public function setExtrasAccessor(string $value)
    {
        $substr = substr($value, 1, -1); //explode(',', $substr)
        if (preg_match_all('#\{(.*?)\}#', $substr, $match) > 1) {
            $setExtra = array_shift($match);
            $setExtraResult = [];
            foreach ($setExtra as $partExtra) {
                $partExtraArray = json_decode($partExtra, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw ConversionException::conversionFailed($value, $this->ids);
                }
                $setExtraResult = array_merge_recursive($setExtraResult, $partExtraArray);
            }
            array_walk($setExtraResult, function (&$v) {
                $v = array_unique($v);
            });
            $val = $setExtraResult;
        } else {
            $val = json_decode($substr, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ConversionException::conversionFailed($value, $this->ids);
            }
        }

        $this->extras = $val;
    }

    /**
     * @param string $value
     */
    public function setStoreExtrasAccessor(string $value)
    {
        $substr = substr($value, 1, -1);
        $substr = str_replace('\\', '', $substr);
        $substr = str_replace('"', '', $substr);
        $storeContainOneProduct = explode('}, ', $substr);

        $newArray = [];
        array_walk($storeContainOneProduct, function ($v, $k) use (&$newArray) {
            $v = str_replace('}', '', $v);
            $v = str_replace('{', '', $v);
            $nums = explode('=>', $v);
            if (isset($nums[0]) && isset($nums[1])) {
                $newExtraArray = [];
                $setExtra = explode(', ', $nums[1]);

                array_walk($setExtra, function ($v, $k) use (&$newExtraArray) {
                    $nums = explode(':', $v);
                    if (isset($nums[0]) && isset($nums[1])) {
                        $newExtraArray[$nums[0]] = trim($nums[1]);
                    }
                });

                $newArray[$nums[0]] = $newExtraArray;
            }
        });
        $storeContainOneProduct = $newArray;

        $this->storeExtras = $storeContainOneProduct;
    }

    /**
     * @param string $value
     */
    public function setStoreImageUrlAccessor(string $value)
    {
        $this->storeImageUrl = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreProductUrlAccessor(string $value)
    {
        $this->storeProductUrl = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreNumberOfEntriesAccessor(string $value)
    {
        $this->storeNumberOfEntries = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStorePriceAccessor(string $value)
    {
        $this->storePrice = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreDescriptionAccessor(string $value)
    {
        $this->storeDescription = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreNamesAccessor(string $value)
    {
        $this->storeNames = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setStoreCreatedAtAccessor(string $value)
    {
        $data = $this->storePropertyAccessor($value);
        $this->storeCreatedAt = $data;

        return $this;
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setStoreSkuAccessor(?string $value = null)
    {
        $data = $this->storePropertyAccessor($value);
        $this->storeSku = $data;

        return $this;
    }

    /**
     * @param string $data
     */
    public function setCreatedAtAccessor(string $data)
    {
        $this->createdAt = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setNamesAccessor(string $data)
    {
        $this->names = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setDescriptionAccessor(string $data)
    {
        $this->description = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setPriceAccessor(string $data)
    {
        $this->price = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setNumberOfEntriesAccessor(string $data)
    {
        $this->numberOfEntries = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setShopAccessor(string $data)
    {
        $this->shop = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setShopRelationIdAccessor(string $data)
    {
        $this->shopRelationId = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setBrandAccessor(string $data)
    {
        $this->brand = $this->simplePropertyAccess($data);
    }

    public function setCurrencyAccessor(string $data)
    {
        $this->currency = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $data
     */
    public function setIdsAccessor(string $data)
    {
        $this->ids = $this->simplePropertyAccess($data);
    }

    /**
     * @param string $storeData
     * @return array
     */
    private function storePropertyAccessor(string $storeData)
    {
        $storeData = str_replace('"', '', $storeData);
        $storeContainOneProduct = explode(',', $storeData);
        $newArray = [];
        array_walk($storeContainOneProduct, function ($v, $k) use (&$newArray) {
            $nums = explode('=>', $v);
            if (isset($nums[0]) && isset($nums[1])) {
                $nums[0] = (int)$nums[0];
                $newArray[$nums[0]] = $nums[1];
            }
        });
        $storeContainOneProduct = $newArray;

        return $storeContainOneProduct;
    }

    /**
     * @param string $data
     * @return array|mixed|string
     */
    private function simplePropertyAccess(string $data)
    {
        $substr = substr($data, 1, -1);
        $val = trim($substr, '"');
        if (preg_match_all('/\"([^\"]*?)\"/', $data, $commonMatches) > 0) {
            $extractValue = array_shift($commonMatches);
            array_walk($extractValue, function (&$v) {
                $v = trim($v, '"');
            });
            if (count($extractValue) > 1) {
                $val = $extractValue;
            }
        } else {
            $val = trim($substr, '"');
            if (count(explode(',', $val)) > 1) {
                $val = explode(',', $val);
            }
        }

        return $val;
    }

    /**
     * @Annotation\PostDeserialize()
     */
    public function postDeserializer()
    {
        if (is_array($this->ids)) {
            $resultArray = [];
            foreach ($this->ids as $id) {
                $id = (int)$id;
                $arr = [
                    'id' => $id,
                    'extras' => $this->storeExtras[$id],
                    'imageUrl' => $this->storeImageUrl[$id],
                    'productUrl' => $this->storeProductUrl[$id],
                    'brand' => $this->brand,
                    'name' => $this->storeNames[$id],
                    'price' => $this->storePrice[$id],
                    'currency' => $this->currency,
                    'shop' => $this->shop,
                    'shopRelationId' => $this->shopRelationId,
                    'description' => $this->storeDescription[$id]
                ];
                $resultArray[] = $arr;
            }
            if (!$this->presentCurrentProduct) {
                $this->presentCurrentProduct = $arr;
            }
            $this->presentAdjacentProducts = $resultArray;
        } elseif (is_string($this->ids)) {
            $currentProduct = [
                'id' => $this->ids,
                'extras' => $this->extras,
                'imageUrl' => $this->storeImageUrl[$this->ids],
                'productUrl' => $this->storeProductUrl[$this->ids],
                'brand' => $this->brand,
                'name' => $this->names,
                'price' => $this->price,
                'currency' => $this->currency,
                'shop' => $this->shop,
                'shopRelationId' => $this->shopRelationId,
                'description' => $this->description
            ];

            $this->presentCurrentProduct = $currentProduct;
        }
    }

    /**
     * @return array
     */
    public function getPresentCurrentProduct(): array
    {
        return $this->presentCurrentProduct;
    }

    /**
     * @param AdjacentProduct $currentProduct
     * @return GroupProductEntity
     */
    public function setCurrentProduct(AdjacentProduct $currentProduct): GroupProductEntity
    {
        $this->currentProduct = $currentProduct;
        return $this;
    }

    /**
     * @return array
     */
    public function getPresentAdjacentProducts(): array
    {
        return $this->presentAdjacentProducts;
    }

    /**
     * @param AdjacentProduct[]|ArrayCollection $adjacentProducts
     * @return GroupProductEntity
     */
    public function setAdjacentProducts($adjacentProducts)
    {
        $this->adjacentProducts = $adjacentProducts;
        return $this;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeSku")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreSkuValue()
    {
        return $this->storeSku;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeCreatedAt")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreCreatedAtValue()
    {
        return $this->storeCreatedAt;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeNames")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreNamesValue()
    {
        return $this->storeNames;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeDescription")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreDescriptionValue()
    {
        return $this->storeDescription;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeExtras")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreExtrasValue()
    {
        return $this->storeExtras;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storePrice")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStorePriceValue()
    {
        return $this->storePrice;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeImageUrl")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreImageUrlValue()
    {
        return $this->storeImageUrl;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeNumberOfEntries")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreNumberOfEntriesValue()
    {
        return $this->storeNumberOfEntries;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeProductUrl")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getStoreProductUrlValue()
    {
        return $this->storeProductUrl;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("extras")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getExtrasValue()
    {
        return $this->extras;
    }
}