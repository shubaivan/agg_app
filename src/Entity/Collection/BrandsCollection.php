<?php

namespace App\Entity\Collection;

use App\Entity\Brand;
use JMS\Serializer\Annotation;

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
     * @return BrandsCollection
     */
    public function setCollection(array $collection): BrandsCollection
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
     * @return BrandsCollection
     */
    public function setCount(int $count): BrandsCollection
    {
        $this->count = $count;
        return $this;
    }
}