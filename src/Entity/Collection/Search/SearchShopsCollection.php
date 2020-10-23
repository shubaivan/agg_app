<?php

namespace App\Entity\Collection\Search;

use App\Entity\Shop;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;

class SearchShopsCollection
{
    const SERIALIZED_GROUP_LIST = 'shop_group_list';

    /**
     * @var array
     * @Annotation\Type("ArrayCollection<App\Entity\Collection\Search\SeparateShopModel>")
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
     */
    private $collection;

    /**
     * @var int
     * @Annotation\Type("int")
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
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
     * @return array|ArrayCollection
     */
    public function getCollection()
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

    /**
     * @param array|ArrayCollection $collection
     */
    public function setCollection($collection): void
    {
        $this->collection = $collection;
    }
}