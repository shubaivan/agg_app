<?php


namespace App\Entity\Collection\Admin\ShopRules;

use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

abstract class ShopRules
{
    /**
     * @var array
     * @Annotation\Type("array")
     * @Annotation\Accessor(setter="setAccessorNegative")
     */
    protected $negative = [];

    /**
     * @var array
     * @Annotation\Type("array")
     * @Annotation\Accessor(setter="setAccessorPositive")
     */
    protected $positive = [];

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
     * @return array
     * @throws \Exception
     */
    public function generateColumnsKeywords()
    {
        $array_merge = array_merge($this->negative, $this->positive);
        if (!count($array_merge)) {
            throw new \Exception('negative and positive was empty');
        }
        return $array_merge;
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
            if ($columnName == 'extras') {
                array_map(function ($extraV, $extraK) use (&$result, $negativeType) {
                    $array_map = array_map(function ($v) {
                        return trim($v);
                    }, array_unique(explode(',', implode(',', $extraV))));

                    $array_filter = array_filter($array_map, function ($v) {
                        if (strlen($v)) {
                            return true;
                        }
                    });
                    $columnName = 'extras';
                    if ($negativeType) {
                        $columnName = '!'.$columnName;
                    }
                    $result[$columnName][$extraK] = array_values(array_unique($array_filter));
                    return ['extras' => [$extraK => $array_map]];
                }, $columnData, array_keys($columnData));

                continue;
            }


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
                $result[$columnName] = array_values(array_unique($array_filter));
            }
        }

        return $result;
    }
}