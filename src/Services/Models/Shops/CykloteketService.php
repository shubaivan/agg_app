<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class CykloteketService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * sku: "40345100"
     * sku: "40345200"
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $sku = $product->getSku();
        $pregSplitSku = preg_split('//', $sku, -1, PREG_SPLIT_NO_EMPTY);
        implode('', array_splice($pregSplitSku, -3, 3));
        $product->setGroupIdentity(implode('', $pregSplitSku));
    }
}