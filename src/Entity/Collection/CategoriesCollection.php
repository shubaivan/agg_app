<?php

namespace App\Entity\Collection;

use App\Entity\Category;
use JMS\Serializer\Annotation;

class CategoriesCollection
{
    /**
     * @var array
     * @Annotation\Groups({Category::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("array<App\Entity\Category>")
     */
    private $collection;

    /**
     * @var int
     * @Annotation\Groups({Category::SERIALIZED_GROUP_LIST})
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