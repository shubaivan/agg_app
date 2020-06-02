<?php

namespace App\Entity\Collection\SearchProducts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\ConversionException;
use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;

class GroupProductEntity
{
    /**
     * @var array
     */
    private $ids;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreBrandAccessor")
     */
    private $storeBrand;

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
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     */
    private $createdAt;

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
     */
    private $price;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     */
    private $numberOfEntries;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     */
    private $shop;

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
     */
    public function setStoreBrandAccessor(string $value)
    {
        $this->storeBrand = $this->storePropertyAccessor($value);
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
    public function setStorePriceAccessor(string $value)
    {
        $this->storePrice = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreNamesAccessor(string $value)
    {
        $this->storeNames = $this->storePropertyAccessor($value);
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
    public function setBrandAccessor(string $data)
    {
        $this->brand = $this->simplePropertyAccess($data);
    }


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
                    throw ConversionException::conversionFailed($value, $this->ids ? array_shift($this->ids) : '');
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
                throw ConversionException::conversionFailed($value, $this->ids ? array_shift($this->ids) : '');
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
        if (is_null($this->ids)) {
            $this->ids = array_keys($newArray);
        }
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

    public function getStoreNamesDataByKey($key)
    {
        return isset($this->storeNames[$key]) ? $this->storeNames[$key] : null;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getStoreExtrasDataByKey($key)
    {
        return isset($this->storeExtras[$key]) ? $this->storeExtras[$key] : null;
    }


    public function getStorePriceDataByKey($key)
    {
        return isset($this->storePrice[$key]) ? $this->storePrice[$key] : null;
    }

    public function getStoreBrandDataByKey($key)
    {
        return isset($this->storeBrand[$key]) ? $this->storeBrand[$key] : null;
    }

    public function getStoreImageUrlDataByKey($key)
    {
        return isset($this->storeImageUrl[$key]) ? $this->storeImageUrl[$key] : null;
    }

    /**
     * @Annotation\PostDeserialize()
     */
    public function postDeserializer()
    {
        if (is_array($this->ids) && count($this->ids) > 0) {
            $currentId = array_shift($this->ids);
            if (!$this->presentCurrentProduct) {
                $this->presentCurrentProduct = $this->transformIdToProductModel($currentId);
            }
            if (count($this->ids) > 0) {
                $this->presentAdjacentProducts = $this->transformIdsToProductModel($this->ids);
            }
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
     * @Annotation\SerializedName("extras")
     * @Annotation\Type("array")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getExtrasValue()
    {
        return $this->extras;
    }

    /**
     * @param array $ids
     * @return array
     */
    private function transformIdsToProductModel(array $ids): array
    {
        $resultArray = [];
        foreach ($ids as $id) {
            $id = (int)$id;
            $resultArray[] = $this->transformIdToProductModel($id);
        }

        return $resultArray;
    }

    /**
     * @param int $id
     * @return array
     */
    private function transformIdToProductModel(int $id): array
    {
        $id = (int)$id;
        $arr = [
            'id' => $id,
            'extras' => $this->getStoreExtrasDataByKey($id),
            'imageUrl' => $this->getStoreImageUrlDataByKey($id),
            'brand' => $this->getStoreBrandDataByKey($id),
            'name' => $this->getStoreNamesDataByKey($id),
            'price' => $this->getStorePriceDataByKey($id),
            'shop' => $this->shop,
        ];
        return  $arr;
    }

}