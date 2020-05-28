<?php

namespace App\Entity\Collection\Search;

use App\Entity\Category;
use JMS\Serializer\Annotation;

class SearchCategoriesCollection
{
    /**
     * @var array
     * @Annotation\Type("array")
     */
    private $collection;

    /**
     * @var int
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
     * @return SearchCategoriesCollection
     */
    public function setCollection(array $collection): SearchCategoriesCollection
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
     * @return SearchCategoriesCollection
     */
    public function setCount(int $count): SearchCategoriesCollection
    {
        $this->count = $count;
        return $this;
    }
}