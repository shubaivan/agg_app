<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class BabyVService implements IdentityGroup
{

    public function identityGroupColumn(Product $product)
    {
        $productUrl = $product->getProductUrl();

        if (preg_match(
            '/articles\/(.|\n*)+\//',
            $productUrl,
            $match
        )) {
            $explodeIdentity = explode('.', array_shift($match));
            array_splice($explodeIdentity, -1, 1);

            $product->setGroupIdentity(implode('.', $explodeIdentity));
        }
    }
}