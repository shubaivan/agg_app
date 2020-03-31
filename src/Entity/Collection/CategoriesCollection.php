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
     * CategoriesCollection constructor.
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
     * @return CategoriesCollection
     */
    public function setCollection(array $collection): CategoriesCollection
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
     * @return CategoriesCollection
     */
    public function setCount(int $count): CategoriesCollection
    {
        $this->count = $count;
        return $this;
    }
}