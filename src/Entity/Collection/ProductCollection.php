<?php

namespace App\Entity\Collection;

use App\Entity\Product;
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

    /**
     * @return array
     */
    public function getRelatedItems(): array
    {
        return $this->relatedItems;
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