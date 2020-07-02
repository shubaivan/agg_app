<?php


namespace App\Entity\Collection;

use App\Entity\Collection\SearchProducts\CommonProduct;
use JMS\Serializer\Annotation;
use App\Entity\Product;

class AvailableTo extends CommonProduct
{
    const GROUP_CREATE = 'create_available_to';
    const GROUP_GET = 'get_available_to';

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableTo::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreProductUrlsAccessor")
     */
    private $storeProductUrls;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableTo::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStorePricesAccessor")
     */
    private $storePrice;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableTo::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreUpdatedAtAccessor")
     */
    private $storeUpdatedAt;

    /**
     * @var array
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableTo::GROUP_CREATE})
     * @Annotation\Accessor(setter="setStoreShopsAccessor")
     */
    private $storeShops;

    /**
     * @var integer
     * @Annotation\Type("integer")
     * @Annotation\Groups({AvailableTo::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $count;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableTo::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $manufacturerArticleNumber;

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
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeProductUrls")
     * @Annotation\Type("array")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    public function getStoreProductUrlsValue()
    {
        return $this->storeProductUrls;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeShops")
     * @Annotation\Type("array")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    public function getStoreShopsValue()
    {
        return $this->storeShops;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storePrices")
     * @Annotation\Type("array")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    public function getStorePricesValue()
    {
        return $this->storePrice;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("storeUpdatedAt")
     * @Annotation\Type("array")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    public function getStoreUpdatedAtValue()
    {
        return $this->storeUpdatedAt;
    }
}