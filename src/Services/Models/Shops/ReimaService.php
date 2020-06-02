<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class ReimaService implements IdentityGroup
{
    /**
     * @param Product $product
     */
    public function identityGroupColumn(Product $product)
    {
        $explode = explode('-', $product->getSku());
        if (count($explode) > 0) {
            $groupIdentity = array_shift($explode);
            $product->setGroupIdentity($groupIdentity);
        } else {
            $groupIdentity = array_shift($explode);
            $product->setGroupIdentity($groupIdentity);
        }
    }
}