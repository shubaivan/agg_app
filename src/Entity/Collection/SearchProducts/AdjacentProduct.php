<?php

namespace App\Entity\Collection\SearchProducts;

use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;

class AdjacentProduct
{
    const GROUP_GENERATE_ADJACENT = 'generate_adjacent_product';

    /**
     * @var int
     * @Annotation\Type("int")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $id;

    /**
     * @var array
     * @Annotation\Type("array")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT})
     */
    private $extras;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $imageUrl;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $brand;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $name;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $price;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $currency;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $shop;

    /**
     * @var integer
     * @Annotation\Type("integer")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $shopRelationId;

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("extras")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET})
     */
    public function getExtrasValue()
    {
        return $this->emptyArrayAsObject($this->extras);
    }

    /**
     * Forces to searialize empty array as json object (i.e. {} instead of []).
     * @see https://stackoverflow.com/q/41588574/878514
     */
    private function emptyArrayAsObject(array $array) {
        if (count($array) == 0) {
            return new \stdClass();
        }
        return $array;
    }
}