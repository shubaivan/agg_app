<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class BabyShopService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $productUrl = $product->getProductUrl();
        if (preg_match(
            '/p\/(.|\n*)+\?/',
            $productUrl,
            $match
        )) {
            $product->setGroupIdentity(array_shift($match));
        }

    }
}