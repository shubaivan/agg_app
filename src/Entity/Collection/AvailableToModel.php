<?php


namespace App\Entity\Collection;

use JMS\Serializer\Annotation;
use App\Entity\Product;

class AvailableToModel
{
    const GROUP_CREATE = 'group_create_available_to_model';
    
    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Accessor(getter="getPriceAccessor")
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
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Annotation\Accessor(getter="getUpdatedAtAccessor")
     */
    private $updatedAt;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({AvailableToModel::GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $productUrl;

    public function getPriceAccessor()
    {
        $price = preg_replace('/\.00/', '', $this->price);

        return $price;
    }

    public function getUpdatedAtAccessor()
    {
        return (new \DateTime())->modify('-1 hour');
    }
}