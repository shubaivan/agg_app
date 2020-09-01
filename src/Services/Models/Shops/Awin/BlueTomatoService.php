<?php


namespace App\Services\Models\Shops\Awin;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class BlueTomatoService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $ean = $product->getEan();
        if (strlen($ean)) {
            $cut = mb_substr($ean, -3);
            $gi = preg_replace('/' . $cut . '/', '', $ean);
            $product->setGroupIdentity($gi);
        } else {
            $product->setGroupIdentity($product->getIdentityUniqData());
        }
    }
}