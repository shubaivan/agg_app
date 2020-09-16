<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutTwoWordFromProductName
{
    public function __invoke(Product $product)
    {
        $preg_split = preg_split('/[\s,\/]+/', $product->getName(), 3);
        if (count($preg_split) > 1) {
            $array_slice = array_slice($preg_split, 0, 2);
            if (count($array_slice)) {
                $product->setGroupIdentity(mb_strtolower(implode('_', $array_slice)));
            }
        }
    }
}