<?php


namespace App\Entity\Collection;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;

class AvailableToCollection
{
    /**
     * @var ArrayCollection|AvailableToModel[]
     * @Annotation\Type("ArrayCollection<App\Entity\Collection\AvailableToModel>")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $collection;
}