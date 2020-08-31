<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class LitenlekerService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $productUrl = $product->getProductUrl();
        $preg_match = preg_match(
            '/([^\/]+$)/',
            $productUrl,
            $matches);
        if ($preg_match) {
            $result = array_shift($matches);
            if ($result) {
                $implode = explode('-', $result);
                if (count($implode) >= 3) {
                    $array_slice = array_slice($implode, 0, 3);
                    $product->setGroupIdentity(implode('-', $array_slice));
                }
            }
        }
    }
}