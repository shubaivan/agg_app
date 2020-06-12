<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class Jultroja implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $product->setGroupIdentity($product->getSku());
    }
}