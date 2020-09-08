<?php


namespace App\Services\Models\Shops\Awin;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class BlueTomatoService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $ean = $product->getEan();
        if (strlen($ean) > 3) {
            $product->setGroupIdentity(mb_substr($ean, 0, -3));
        }
    }
}