<?php


namespace App\Entity\Collection;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use App\Entity\Collection\AvailableToDTO;

class AvailableToCollection
{
    /**
     * @var ArrayCollection|AvailableToDTO[]
     * @Annotation\Type("ArrayCollection<App\Entity\Collection\AvailableToDTO>")
     * @Annotation\Groups({AvailableToDTO::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $collection;

    /**
     * @return AvailableToDTO[]|ArrayCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }


}