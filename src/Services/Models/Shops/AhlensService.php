<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class AhlensService implements IdentityGroup
{

    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $productUrl = $product->getProductUrl();
        preg_replace('/' . $sku . '/', '', $productUrl);

        if (preg_match(
            '/\-(.|\n*)+\//',
            $productUrl,
            $match
        )) {
            $product->setGroupIdentity(array_shift($match));
        }
    }
}