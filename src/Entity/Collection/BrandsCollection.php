<?php

namespace App\Entity\Collection;

use JMS\Serializer\Annotation;
use App\Entity\Brand;

class BrandsCollection
{
    /**
     * @var array
     * @Annotation\Groups({Brand::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("array<App\Entity\Brand>")
     */
    private $collection;

    /**
     * @var int
     * @Annotation\Groups({Brand::SERIALIZED_GROUP_LIST})
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