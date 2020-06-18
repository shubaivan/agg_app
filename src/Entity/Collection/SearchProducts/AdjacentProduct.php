<?php

namespace App\Entity\Collection\SearchProducts;

use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Product;

class AdjacentProduct extends CommonProduct
{
    const GROUP_GENERATE_ADJACENT = 'generate_adjacent_product';

    /**
     * @var int
     * @Annotation\Type("int")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *      SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $id;

    /**
     * @var array
     * @Annotation\Type("array")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT, Product::SERIALIZED_GROUP_LIST})
     */
    private $extras;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $imageUrl;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $brand;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $name;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $price;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $currency;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $shop;

    /**
     * @var integer
     * @Annotation\Type("integer")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $shopRelationId;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $productUrl;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $description;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    private $instock;

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("extras")
     * @Annotation\Groups({SearchProductCollection::GROUP_GET, Product::SERIALIZED_GROUP_LIST})
     */
    public function getExtrasValue()
    {
        $ex = $this->extras ?? [];
        return $this->emptyArrayAsObject($ex);
    }
}