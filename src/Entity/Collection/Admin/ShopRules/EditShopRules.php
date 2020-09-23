<?php


namespace App\Entity\Collection\Admin\ShopRules;

use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

class EditShopRules extends ShopRules
{
    /**
     * @var int
     * @Annotation\Type("int")
     * @Assert\NotBlank()
     */
    private $shopRuleId;

    /**
     * @return int
     */
    public function getShopRuleId(): int
    {
        return $this->shopRuleId;
    }
}