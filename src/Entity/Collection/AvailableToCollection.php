<?php


namespace App\Entity\Collection;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use App\Entity\Collection\AvailableTo;

class AvailableToCollection
{
    /**
     * @var ArrayCollection|AvailableTo[]
     * @Annotation\Type("ArrayCollection<App\Entity\Collection\AvailableTo>")
     * @Annotation\Groups({AvailableTo::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $collection;
}