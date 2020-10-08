<?php


namespace App\Entity\Collection\Admin\ShopRules;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

class CreateShopRules extends ShopRules
{
    /**
     * @var string
     * @Annotation\Type("string")
     * @Assert\NotBlank()
     */
    private $shopName;

    /**
     * @return string
     */
    public function getShopName(): string
    {
        return $this->shopName;
    }
}