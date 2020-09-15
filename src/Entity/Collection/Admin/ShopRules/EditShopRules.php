<?php


namespace App\Entity\Collection\Admin\ShopRules;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

class EditShopRules
{
    /**
     * @var int
     * @Annotation\Type("int")
     * @Assert\NotBlank()
     */
    private $shopRuleId;

    /**
     * @var array
     * @Annotation\Type("array")
     * @Annotation\Accessor(setter="setAccessorNegative")
     * @Assert\NotBlank()
     */
    private $negative;

    /**
     * @var array
     * @Annotation\Type("array")
     * @Annotation\Accessor(setter="setAccessorPositive")
     * @Assert\NotBlank()
     */
    private $positive;

    public function setAccessorNegative(array $data)
    {
        $this->setNegative($this->prepareTypeArray($data, true));
    }

    public function setAccessorPositive(array $data)
    {
        $this->setPositive($this->prepareTypeArray($data));
    }

    /**
     * @param array $negative
     */
    public function setNegative(array $negative): void
    {
        $this->negative = $negative;
    }

    /**
     * @param array $positive
     */
    public function setPositive(array $positive): void
    {
        $this->positive = $positive;
    }

    /**
     * @return int
     */
    public function getShopRuleId(): int
    {
        return $this->shopRuleId;
    }

    public function generateColumnsKeywords()
    {
        return array_merge($this->negative, $this->positive);
    }

    /**
     * @param array $data
     * @param bool $negativeType
     * @return array
     */
    private function prepareTypeArray(array $data, $negativeType = false): array
    {
        $result = [];
        foreach ($data as $columnName => $columnData) {
            $array_map = array_map(function ($v) {
                return trim($v);
            }, array_unique(explode(',', implode(',', $columnData))));
            $array_filter = array_filter($array_map, function ($v) {
                if (strlen($v)) {
                    return true;
                }
            });
            if (count($array_filter)) {
                if ($negativeType) {
                    $columnName = '!'.$columnName;
                }
                $result[$columnName] = array_values($array_filter);
            }
        }

        return $result;
    }
}