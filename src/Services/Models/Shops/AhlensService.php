<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class AhlensService implements IdentityGroup
{

    public function identityGroupColumn(Product $product)
    {
        $productUrl = $product->getProductUrl();
        $lastChar = substr($productUrl, -1);
        if ($lastChar == '/') {
            $productUrl = substr($productUrl, 0, -1);
        }
        $productUrl = preg_replace("/[^\/]+$/", '', $productUrl);
        $lastChar = substr($productUrl, -1);
        while ($lastChar == '/') {
            $productUrl = substr($productUrl, 0, -1);
            $lastChar = substr($productUrl, -1);
        }

        if (preg_match("/[^\/]+$/", $productUrl, $matches) > 0) {
            $sku = $product->getSku();
            $skuId = mb_substr($sku, 0, 3);
            $product->setGroupIdentity($skuId . array_shift($matches));
        }
    }
}