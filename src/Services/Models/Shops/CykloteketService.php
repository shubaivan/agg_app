<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class CykloteketService implements IdentityGroup
{
    /**
     * sku: "40345100"
     * sku: "40345200"
     *
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $pregSplitSku = preg_split('//', $sku, -1, PREG_SPLIT_NO_EMPTY);
        implode('', array_splice($pregSplitSku, -3, 3));
        $product->setGroupIdentity(implode('', $pregSplitSku));
    }
}