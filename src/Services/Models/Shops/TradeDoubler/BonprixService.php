<?php

namespace App\Services\Models\Shops\TradeDoubler;

use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;

class BonprixService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $identityUniqData = $product->getIdentityUniqData();
        $ean = $product->getEan();
        if ($ean) {
            $identityUniqData = mb_substr($ean, 0, 9);
        }
        $product->setGroupIdentity($identityUniqData);
    }
}