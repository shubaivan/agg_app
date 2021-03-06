<?php

namespace App\Entity\Collection;

use App\Entity\Shop;
use JMS\Serializer\Annotation;

class ShopsCollection
{
    /**
     * @var array
     * @Annotation\Groups({Shop::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("array<App\Entity\Shop>")
     */
    private $collection;

    /**
     * @var int
     * @Annotation\Groups({Shop::SERIALIZED_GROUP_LIST})
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