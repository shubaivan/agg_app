<?php


namespace App\Services\Models\Shops\Awin;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class EllosSEService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        if (strlen($sku)) {
            $product->setGroupIdentity(mb_substr($sku, 0, -2));
        }
    }
}