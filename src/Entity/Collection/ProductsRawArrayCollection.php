<?php

namespace App\Entity\Collection;

use App\Entity\Product;
use JMS\Serializer\Annotation;

class ProductsRawArrayCollection
{
    /**
     * @var array
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("array")
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
}