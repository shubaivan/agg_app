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
     * @var AvailableToCollection
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Type("App\Entity\Collection\AvailableToCollection")
     */
    private $availableTo;

    /**
     * ProductByIdCollection constructor.
     * @param SearchProductCollection $currentProduct
     * @param AvailableToCollection $availableTo
     */
    public function __construct(SearchProductCollection $currentProduct, AvailableToCollection $availableTo)
    {
        $this->currentProduct = $currentProduct;
        $this->availableTo = $availableTo;
    }


}