<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class FullProductName
{
    public function __invoke(Product $product)
    {
        $name = $product->getName();
        $name = preg_replace('/[\s+,.]+/', '_', $name);

        if (strlen($name)) {
            $product->setGroupIdentity(mb_strtolower($name));
        }
    }
}