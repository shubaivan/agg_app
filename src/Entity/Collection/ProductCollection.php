<?php

namespace App\Entity\Collection;

use App\Entity\Product;
use Doctrine\DBAL\Types\ConversionException;
use JMS\Serializer\Annotation;

class ProductCollection
{
    /**
     * @var Product
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("App\Entity\Product")
     */
    private $product;

    /**
     * @var array
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("array")
     * @Annotation\Accessor(getter="getAccessorRelatedItems")
     */
    private $relatedItems;

    /**
     * ProductCollection constructor.
     * @param array $relatedItems
     * @param Product $product
     */
    public function __construct(array $relatedItems, Product $product)
    {
        $this->relatedItems = $relatedItems;
        $this->product = $product;
    }

    public function getAccessorRelatedItems()
    {
        return $this->getRelatedItems();
    }

    /**
     * @return array
     */
    public function getRelatedItems(): array
    {
        $array_map = array_map(function ($key) {
            if (isset($key['extras'])) {
                $val = json_decode($key['extras'], true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw ConversionException::conversionFailed($key['extras'], $key['sku']);
                }
                $key['extras'] = $val;
            }

            return $key;
        }, $this->relatedItems);

        return $array_map;
    }

    /**
     * @param array $relatedItems
     * @return ProductCollection
     */
    public function setRelatedItems(array $relatedItems): ProductCollection
    {
        $this->relatedItems = $relatedItems;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return ProductCollection
     */
    public function setProduct(Product $product): ProductCollection
    {
        $this->product = $product;
        return $this;
    }
}