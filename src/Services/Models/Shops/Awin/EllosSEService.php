<?php


namespace App\Services\Models\Shops\Awin;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class EllosSEService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $identity = [];
        if (strlen($sku)) {
            $identity[] = mb_substr($sku, 0, -3);
        }
        $ean = $product->getEan();
        if (strlen($ean)) {
            $identity[] = mb_substr($ean, 0, -2);
        }
        if (count($identity)) {
            $product->setGroupIdentity(implode('_', $identity));
        }
    }
}