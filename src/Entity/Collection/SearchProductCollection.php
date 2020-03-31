<?php

namespace App\Entity\Collection;

use App\Entity\Product;
use JMS\Serializer\Annotation;

class SearchProductCollection
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
     * SearchProductCollection constructor.
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
     * @return SearchProductCollection
     */
    public function setCollection(array $collection): SearchProductCollection
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
     * @return SearchProductCollection
     */
    public function setCount(int $count): SearchProductCollection
    {
        $this->count = $count;
        return $this;
    }
}