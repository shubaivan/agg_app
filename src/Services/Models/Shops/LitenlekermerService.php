<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class LitenlekermerService implements IdentityGroup
{
    /**
     * @param Product $product
     */
    public function identityGroupColumn(Product $product)
    {
        $explode = explode('-', $product->getSku());
        if (count($explode) > 2) {
            $firstPartIdentity = array_shift($explode);
            $secondPartIdentity = array_shift($explode);
            $product->setGroupIdentity($firstPartIdentity . $secondPartIdentity);
        } else {
            $groupIdentity = array_shift($explode);
            $product->setGroupIdentity($groupIdentity);
        }
    }
}