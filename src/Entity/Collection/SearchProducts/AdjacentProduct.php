<?php

namespace App\Entity\Collection\SearchProducts;

use JMS\Serializer\Annotation;
use App\Entity\Collection\SearchProductCollection;

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
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
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
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET})
     */
    private $productUrl;

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
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $shopRelationId;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, SearchProductCollection::GROUP_GET})
     */
    private $description;
}