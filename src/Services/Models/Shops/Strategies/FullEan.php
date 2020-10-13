<?php


namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class FullEan
{
    public function __invoke(Product $product)
    {
        if ($product->getEan()) {
            $product->setGroupIdentity($product->getEan());
        }
    }
}