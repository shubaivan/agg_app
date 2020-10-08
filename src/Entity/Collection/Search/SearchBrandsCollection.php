<?php

namespace App\Entity\Collection\Search;

use App\Entity\Brand;
use JMS\Serializer\Annotation;

class SearchBrandsCollection
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
     * BrandsCollection constructor.
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
     * @return SearchBrandsCollection
     */
    public function setCollection(array $collection): SearchBrandsCollection
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
     * @return SearchBrandsCollection
     */
    public function setCount(int $count): SearchBrandsCollection
    {
        $this->count = $count;
        return $this;
    }
}