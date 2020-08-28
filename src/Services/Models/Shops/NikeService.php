<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class NikeService implements IdentityGroup
{

    public function identityGroupColumn(Product $product)
    {
        $name = $product->getName();
        $product->setGroupIdentity($name);
    }
}