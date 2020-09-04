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
            $cut = mb_substr($sku, -2);
            $gi = preg_replace('/' . $cut . '/', '', $sku);
            $product->setGroupIdentity($gi);
        } else {
            $product->setGroupIdentity($product->getIdentityUniqData());
        }
    }
}