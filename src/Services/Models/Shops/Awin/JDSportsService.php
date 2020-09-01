<?php


namespace App\Services\Models\Shops\Awin;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class JDSportsService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $identity = [];
        $sku = $product->getSku();
        if ($sku) {
            $identity[] = mb_substr($sku, 0, 9);
        }
        $ean = $product->getEan();
        if ($ean) {
            $identity[] = mb_substr($ean, 0, 6);
        }
        if (count($identity)) {
            $product->setGroupIdentity(implode('_', $identity));
        } else {
            $product->setGroupIdentity($product->getIdentityUniqData());
        }
    }
}