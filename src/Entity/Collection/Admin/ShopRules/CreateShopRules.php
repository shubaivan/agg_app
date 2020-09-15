<?php


namespace App\Entity\Collection\Admin\ShopRules;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

class CreateShopRules extends ShopRules
{
    /**
     * @var int
     * @Annotation\Type("int")
     * @Assert\NotBlank()
     */
    private $shopId;

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }
}