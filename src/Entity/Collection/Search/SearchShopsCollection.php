<?php

namespace App\Entity\Collection\Search;

use App\Entity\Shop;
use JMS\Serializer\Annotation;

class SearchShopsCollection
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
     * ShopsCollection constructor.
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
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}