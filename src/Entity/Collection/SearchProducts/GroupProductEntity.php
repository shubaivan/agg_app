<?php

namespace App\Entity\Collection\SearchProducts;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\ConversionException;
use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;

class GroupProductEntity extends CommonProduct
{
    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreProductUrlAccessor")
     */
    private $storeProductUrl;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreManufacturerArticleNumberAccessor")
     */
    private $storeManufacturerArticleNumber;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreEanAccessor")
     */
    private $storeEan;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreSkuAccessor")
     */
    private $storeSku;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreDescriptionAccessor")
     */
    private $storeDescription;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreInstockAccessor")
     */
    private $storeInstock;

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

//    /**
//     * @var array
//     * @Annotation\Type("string")
//     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
//     * @Annotation\Accessor(setter="setStoreCurrencyAccessor")
//     */
//    private $storeCurrency;

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
     * @var integer
     * @Annotation\Type("integer")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     */
    private $shopRelationId;

    /**
     * @var ArrayCollection|AdjacentProduct[]
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $adjacentProducts = [];

    /**
     * @var AdjacentProduct
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $currentProduct;

    /**
     * @var string
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $rangePrice;

    /**
     * @var integer
     * @Annotation\Type("integer")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE})
     */
    private $productById;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({
     *     SearchProductCollection::GROUP_CREATE,
     *     SearchProductCollection::GROUP_GET,
     *     Product::SERIALIZED_GROUP_LIST})
     */
    private $groupIdentity;

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
    public function setStoreProductUrlAccessor(string $value)
    {
        $this->storeProductUrl = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreManufacturerArticleNumberAccessor(string $value)
    {
        $this->storeManufacturerArticleNumber = $this->storePropertyAccessor($value);
    }

    public function setStoreEanAccessor(string $value)
    {
        $this->storeEan = $this->storePropertyAccessor($value);
    }

    public function setStoreSkuAccessor(string $value)
    {
        $this->storeSku = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreInstockAccessor(string $value)
    {
        $this->storeInstock = $this->storePropertyAccessor($value);
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
        if (is_array($this->storePrice)) {
            $max = max($this->storePrice);
            $min = min($this->storePrice);

            $max = preg_replace('/.00/', '', $max);
            $min = preg_replace('/.00/', '', $min);

            $this->rangePrice = ((float)$max != (float)$min ? $min . ' - ' . $max : $max);
        }
    }

//    /**
//     * @param string $value
//     */
//    public function setStoreCurrencyAccessor(string $value)
//    {
//        $this->storeCurrency = $this->storePropertyAccessor($value);
//    }

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
                if (is_array($v)) {
                    $v = array_unique($v);
                }
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
        $substr = str_replace(self::NULL, '"{"' . self::NULL . '"}"', $value);
        $substr = str_replace('\\', '', $substr);

        $storeContainOneProduct = explode('}", "', $substr);
        $newArray = [];
        array_walk($storeContainOneProduct, function ($v, $k) use (&$newArray) {

            $nums = explode('=>', $v);
            if (isset($nums[0]) && isset($nums[1])) {
                $trimExtra = $nums[1];

                if (mb_substr_count($trimExtra, self::NULL)) {
                    return;
                }
                if (substr($trimExtra, -1) !== '}' && substr($trimExtra, -2) !== '}"') {
                    $trimExtra .= '}';
                }
                $trimExtra = trim($trimExtra, "\"");
                $trimId = (int)trim($nums[0], "\"");

                $decodeTrimExtra = json_decode($trimExtra, true);

                if ($decodeTrimExtra) {
                    $newArray[$trimId] = $decodeTrimExtra;
                }
            }
        });
        $storeContainOneProduct = $newArray;

        $this->storeExtras = $storeContainOneProduct;
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
        return isset($this->storeExtras[$key]) ? $this->storeExtras[$key] : [];
    }

    public function getStorePriceDataByKey($key)
    {
        return isset($this->storePrice[$key]) ? $this->storePrice[$key] : null;
    }

//    public function getStoreCurrencyDataByKey($key)
//    {
//        return isset($this->storeCurrency[$key]) ? $this->storeCurrency[$key] : null;
//    }

    public function getStoreBrandDataByKey($key)
    {
        return isset($this->storeBrand[$key]) ? $this->storeBrand[$key] : null;
    }

    public function getStoreProductUrlDataByKey($key)
    {
        return isset($this->storeProductUrl[$key]) ? $this->storeProductUrl[$key] : null;
    }

    public function getStoreDescriptionDataByKey($key)
    {
        return isset($this->storeDescription[$key]) ? $this->storeDescription[$key] : null;
    }

    public function getStoreInstockDataByKey($key)
    {
        return isset($this->storeInstock[$key]) ? $this->storeInstock[$key] : null;
    }

    public function getStoreImageUrlDataByKey($key)
    {
        return isset($this->storeImageUrl[$key]) ? $this->storeImageUrl[$key] : null;
    }

    public function getStoreManufacturerArticleNumberDataByKey($key)
    {
        return isset($this->storeManufacturerArticleNumber[$key]) ? $this->storeManufacturerArticleNumber[$key] : null;
    }

    public function getStoreEanDataByKey($key)
    {
        return isset($this->storeEan[$key]) ? $this->storeEan[$key] : null;
    }

    public function getStoreSkuDataByKey($key)
    {
        return isset($this->storeSku[$key]) ? $this->storeSku[$key] : null;
    }

    /**
     * @Annotation\PostDeserialize()
     */
    public function postDeserializer()
    {
        if (is_array($this->ids) && count($this->ids) > 0) {
            if ($this->productById) {
                $currentId = $this->productById;
            } else {
                $currentId = array_shift($this->ids);
            }

            if (!$this->presentCurrentProduct) {
                $this->presentCurrentProduct = $this->transformIdToProductModel($currentId);
            }
            if (count($this->ids) > 0) {
                $ids = $this->ids;
                if (($key = array_search($currentId, $ids)) !== false) {
                    unset($ids[$key]);
                }
                $this->presentAdjacentProducts = $this->transformIdsToProductModel($ids);
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
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
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
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
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
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
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
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    public function getStoreImageUrlValue()
    {
        return $this->storeImageUrl;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("extras")
     * @Annotation\Type("array<string, array<string>>")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    public function getExtrasValue()
    {
        $ex = $this->extras ?? [];
        return $this->emptyArrayAsObject($ex);
    }

    /**
     * @return array|string
     */
    private function getStoreManufacturerArticleNumber()
    {
        return $this->storeManufacturerArticleNumber;
    }

    /**
     * @return array|string
     */
    private function getStoreEan()
    {
        return $this->storeEan;
    }

    /**
     * @return array|string
     */
    private function getStoreSku()
    {
        return $this->storeSku;
    }

    /**
     * @return array
     */
    public function getAvailableToData()
    {
        $aft = [];
        $storeManufacturerArticleNumberDataByKey = $this
            ->getStoreManufacturerArticleNumberDataByKey($this->currentProduct->getId());
        if ($storeManufacturerArticleNumberDataByKey) {
            $aft['manufacturer_article_number'] = [
                'current' => $storeManufacturerArticleNumberDataByKey
            ];
        }
        $storeEanDataByKey = $this
            ->getStoreEanDataByKey($this->currentProduct->getId());
        if ($storeEanDataByKey) {
            $aft['ean'] = [
                'current' => $storeEanDataByKey
            ];
        }
        $storeSkuDataByKey = $this
            ->getStoreSkuDataByKey($this->currentProduct->getId());
        if ($storeSkuDataByKey) {
            $aft['sku'] = [
                'current' => $storeSkuDataByKey
            ];
        }
        return $aft;
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
            'currency' => 'SEK',
            'shop' => $this->shop,
            'shopRelationId' => $this->shopRelationId,
            'productUrl' => $this->getStoreProductUrlDataByKey($id),
            'description' => $this->getStoreDescriptionDataByKey($id),
            'instock' => $this->getStoreInstockDataByKey($id)
        ];
        return $arr;
    }

}