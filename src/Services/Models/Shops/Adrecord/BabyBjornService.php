<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class BabyBjornService implements IdentityGroup
{

    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $sku = mb_substr($sku, 0, 7);
        $product->setGroupIdentity($sku);
    }
}