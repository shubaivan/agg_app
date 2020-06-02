<?php

namespace App\Entity\Collection\Search;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use App\Entity\Collection\SearchProducts\GroupProductEntity;

class SearchProductCollection
{
    const GROUP_CREATE = 'group_product_create';
    const GROUP_GET = 'group_product_get';

    /**
     * @var ArrayCollection|GroupProductEntity[]
     * @Annotation\Type("ArrayCollection<App\Entity\Collection\SearchProducts\GroupProductEntity>")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE, SearchProductCollection::GROUP_GET})
     */
    private $collection;

    /**
     * @var int
     * @Annotation\Type("int")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE, SearchProductCollection::GROUP_GET})
     */
    private $count;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchProductCollection::GROUP_CREATE, SearchProductCollection::GROUP_GET})
     */
    private $uniqIdentificationQuery;

    /**
     * @return GroupProductEntity[]|ArrayCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @Annotation\PostDeserialize()
     */
    public function postDeserializer()
    {
        $this->count < 1 ? $this->uniqIdentificationQuery = '' : '';
    }
}