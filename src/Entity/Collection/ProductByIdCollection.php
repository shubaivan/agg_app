<?php

namespace App\Entity\Collection;

use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Product;
use JMS\Serializer\Annotation;

class ProductByIdCollection
{
    /**
     * @var SearchProductCollection
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("App\Entity\Collection\Search\SearchProductCollection")
     */
    private $currentProduct;

    /**
     * @var SearchProductCollection
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("App\Entity\Collection\Search\SearchProductCollection")
     */
    private $relatedItems;

    /**
     * ProductByIdCollection constructor.
     * @param SearchProductCollection $currentProduct
     * @param SearchProductCollection $relatedItems
     */
    public function __construct(SearchProductCollection $currentProduct, SearchProductCollection $relatedItems)
    {
        $this->currentProduct = $currentProduct;
        $this->relatedItems = $relatedItems;
    }
}