<?php


namespace App\Entity\Collection;

use App\Entity\Collection\SearchProducts\CommonProduct;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use App\Entity\Product;
use App\Entity\Collection\AvailableToModel;

class AvailableToDTO extends CommonProduct
{
    const GROUP_CREATE = 'create_available_to';
    const GROUP_GET = 'get_available_to';

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreProductUrlsAccessor")
     */
    private $storeProductUrls;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStorePricesAccessor")
     */
    private $storePrice;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreCurrencyAccessor")
     */
    private $storeCurrency;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreUpdatedAtAccessor")
     */
    private $storeUpdatedAt;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreShopsAccessor")
     */
    private $storeShops;

    /**
     * @var integer
     * @Annotation\Type("integer")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $count;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $manufacturerArticleNumber;

    /**
     * @var ArrayCollection|AvailableToModel[]
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    private $availableToModel;

    /**
     * @var array
     */
    private $presentAvailableToModel = [];

    /**
     * @param string $value
     */
    public function setStoreCurrencyAccessor(string $value)
    {
        $this->storeCurrency = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreUpdatedAtAccessor(string $value)
    {
        $this->storeUpdatedAt = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStorePricesAccessor(string $value)
    {
        $this->storePrice = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreProductUrlsAccessor(string $value)
    {
        $this->storeProductUrls = $this->storePropertyAccessor($value);
    }

    /**
     * @param string $value
     */
    public function setStoreShopsAccessor(string $value)
    {
        $this->storeShops = $this->storePropertyAccessor($value);
    }

    /**
     * @Annotation\PostDeserialize()
     */
    public function postDeserializer()
    {
        if (is_array($this->ids) && count($this->ids) > 0) {
            if (!$this->presentAvailableToModel) {
                $this->presentAvailableToModel = $this->transformIdsToProductModel($this->ids);
            }
        }
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
            'shop' => $this->getStoreShopsDataByKey($id),
            'updatedAt' => $this->getStoreUpdatedAtDataByKey($id),
            'price' => $this->getStorePriceDataByKey($id),
            'currency' => $this->getStoreCurrencyDataByKey($id),
            'productUrl' => $this->getStoreProductUrlDataByKey($id),
        ];
        return  $arr;
    }

    private function getStoreShopsDataByKey($key)
    {
        return isset($this->storeShops[$key]) ? $this->storeShops[$key] : [];
    }

    private function getStoreUpdatedAtDataByKey($key)
    {
        return isset($this->storeUpdatedAt[$key]) ? $this->storeUpdatedAt[$key] : [];
    }

    private function getStoreCurrencyDataByKey($key)
    {
        return isset($this->storeCurrency[$key]) ? $this->storeCurrency[$key] : [];
    }

    private function getStoreProductUrlDataByKey($key)
    {
        return isset($this->storeProductUrls[$key]) ? $this->storeProductUrls[$key] : [];
    }

    private function getStorePriceDataByKey($key)
    {
        return isset($this->storePrice[$key]) ? $this->storePrice[$key] : [];
    }

    /**
     * @return array
     */
    public function getPresentAvailableToModel(): array
    {
        return $this->presentAvailableToModel;
    }

    /**
     * @param AvailableToModel[]|ArrayCollection $availableToModel
     * @return AvailableToDTO
     */
    public function setAvailableToModel($availableToModel)
    {
        $this->availableToModel = $availableToModel;
        return $this;
    }
}