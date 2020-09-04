<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class AhlensService implements IdentityGroup
{

    public function identityGroupColumn(Product $product)
    {
        $productUrl = $product->getProductUrl();
        $lastChar = mb_substr($productUrl, -1);
        if ($lastChar == '/') {
            $productUrl = mb_substr($productUrl, 0, -1);
        }
        $productUrl = preg_replace("/[^\/]+$/", '', $productUrl);
        $lastChar = mb_substr($productUrl, -1);
        while ($lastChar == '/') {
            $productUrl = mb_substr($productUrl, 0, -1);
            $lastChar = mb_substr($productUrl, -1);
        }

        if (preg_match("/[^\/]+$/", $productUrl, $matches) > 0) {
            $sku = $product->getSku();
            $skuId = mb_substr($sku, 0, 3);
            $product->setGroupIdentity($skuId . array_shift($matches));
        }
    }
}