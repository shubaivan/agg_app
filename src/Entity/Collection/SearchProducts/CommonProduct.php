<?php

namespace App\Entity\Collection\SearchProducts;

use App\Entity\Product;
use App\Entity\SlugAbstract;

abstract class CommonProduct
{
    const NULL = 'NULL';

    /**
     * @var array
     */
    protected $ids;

    /**
     * Forces to searialize empty array as json object (i.e. {} instead of []).
     * @see https://stackoverflow.com/q/41588574/878514
     */
    protected function emptyArrayAsObject(array $array) {
        if (count($array) == 0) {
            return new \stdClass();
        }
        if (isset($array[Product::SIZE])) {
            $array[Product::SIZE] = array_unique($array[Product::SIZE]);
        }
        return $array;
    }

    /**
     * @param string $storeData
     * @return array
     */
    protected function storePropertyAccessor(string $storeData)
    {
        $storeData = str_replace(self::NULL, '"'.self::NULL.'"', $storeData);
        $storeContainOneProduct = explode('", "', $storeData);
        $newArray = [];
        array_walk($storeContainOneProduct, function ($v, $k) use (&$newArray) {
            $nums = explode('=>', $v);
            if (isset($nums[0]) && isset($nums[1])) {
                $newKey = str_replace('"', '', $nums[0]);
                $newValue = str_replace('"', '', $nums[1]);
                if ($newValue != self::NULL)
                {
                    $newKey = (int)$newKey;
                    $newArray[$newKey] = $newValue;
                }
            }
        });
        if (is_null($this->ids) && is_array($newArray) && count($newArray) > 0) {
            $this->ids = array_keys($newArray);
        }
        $storeContainOneProduct = $newArray;

        return $storeContainOneProduct;
    }
}