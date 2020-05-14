<?php

namespace App\Entity\Collection\SearchProducts;

use Doctrine\DBAL\Types\ConversionException;
use JMS\Serializer\Annotation;
use App\Entity\Collection\SearchProductCollection;

class GroupAdjacent
{
    /**
     * @var
     * @Annotation\Type("ArrayCollection<App\Entity\Collection\SearchProducts\AdjacentProduct>")
     * @Annotation\Groups({AdjacentProduct::GROUP_GENERATE_ADJACENT})
     */
    private $adjacentProducts;

    /**
     * @return mixed
     */
    public function getAdjacentProducts()
    {
        return $this->adjacentProducts;
    }
}