<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class StorAndLitenService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $cut = substr($sku, 4);
        $mb_strtolower = mb_strtolower($product->getBrand());
        $product->setGroupIdentity($cut.'_'.$mb_strtolower);
    }
}