<?php


namespace App\Services\Models\Shops\Awin;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class EllosSEService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $ean = $product->getEan();
        if (strlen($ean) >= 13) {
            $cut = mb_substr($ean, -3);
            $gi = preg_replace('/' . $cut . '/', '', $ean);
            $product->setGroupIdentity($gi);
        } elseif (preg_match('/\+/', $ean)) {
            $sku = $product->getSku();
            $cut = mb_substr($sku, -2);
            $gi = preg_replace('/' . $cut . '/', '', $sku);
            $product->setGroupIdentity($gi);
        } else {
            $product->setGroupIdentity($product->getIdentityUniqData());
        }
    }
}