<?php

namespace App\Entity\Collection\SearchProducts;

use App\Entity\Product;

abstract class CommonProduct
{
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
}