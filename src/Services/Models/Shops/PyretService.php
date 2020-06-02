<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class PyretService implements IdentityGroup
{
    /**
     * @param Product $product
     */
    public function identityGroupColumn(Product $product)
    {
        if (preg_match('/([^\/]+$)/', $product->getProductUrl(), $matches)) {
            $lastPartUrl = array_shift($matches);
            $explodeLastPartUrl = explode('-', $lastPartUrl);
            if (count($explodeLastPartUrl) > 2) {
                $selectGroupIdentity = array_slice($explodeLastPartUrl, -2, 1);
                $groupIdentity = array_shift($selectGroupIdentity);
                $product->setGroupIdentity($groupIdentity);
            }
        }
    }
}