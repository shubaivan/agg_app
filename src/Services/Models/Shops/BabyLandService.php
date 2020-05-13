<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class BabyLandService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $name = $product->getName();
        preg_replace("/\([^)]+\)/", "", $name);
        if (strlen($name) > 0) {
            $product->setGroupIdentity(trim($name));
        }
    }
}