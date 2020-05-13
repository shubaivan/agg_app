<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class LindexService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $lastPartUrl = preg_match('/([^\/]+$)/', $product->getProductUrl());
        $explodeLastPartUrl = explode('-', $lastPartUrl);
        if (count($explodeLastPartUrl) > 1) {
            $groupIdentity = array_shift($explodeLastPartUrl);
            $product->setGroupIdentity($groupIdentity);
        }
    }
}