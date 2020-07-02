<?php


namespace App\Entity\Collection;

use JMS\Serializer\Annotation;
use App\Entity\Product;

class AvailableToModel
{
    const GROUP_CREATE = 'group_create_available_to_model';

    /**
     * @var int
     * @Annotation\Type("int")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $id;


    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $price;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $currency;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $shop;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $updatedAt;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $productUrl;
}