<?php


namespace App\Services\Models\Shops\TradeDoubler;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class BonprixService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $identityUniqData = $product->getIdentityUniqData();
        $ean = $product->getEan();
        if ($ean) {
            $identityUniqData = mb_substr($ean, 0, 9);
        }
        $product->setGroupIdentity($identityUniqData);
    }
}