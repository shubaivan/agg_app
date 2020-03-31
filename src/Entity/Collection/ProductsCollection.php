<?php

namespace App\Entity\Collection;

use App\Entity\Product;
use JMS\Serializer\Annotation;

class ProductsCollection
{
    /**
     * @var array
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("array<App\Entity\Product>")
     */
    private $collection;

    /**
     * @var int
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("int")
     */
    private $count;

    /**
     * ProductsCollection constructor.
     * @param array $collection
     * @param int $count
     */
    public function __construct(array $collection, int $count)
    {
        $this->collection = $collection;
        $this->count = $count;
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

    /**
     * @param array $collection
     * @return ProductsCollection
     */
    public function setCollection(array $collection): ProductsCollection
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return ProductsCollection
     */
    public function setCount(int $count): ProductsCollection
    {
        $this->count = $count;
        return $this;
    }
}