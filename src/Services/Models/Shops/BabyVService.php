<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class BabyVService implements IdentityGroup
{
    /**
     * productUrl: "https://www.babyv.se/sv/articles/1.37.18/babybjorn-babybjorn-balance-bliss-mesh-antracitgra"
     * productUrl: "https://www.babyv.se/sv/articles/1.37.9/babybjorn-babybjorn-balance-soft-jersey-morkgragra"
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $productUrl = $product->getProductUrl();

        if (preg_match(
            '/articles\/(.|\n*)+\//',
            $productUrl,
            $match
        )) {
            $identityValue = array_shift($match);
            $identityValue = str_replace('articles/', '', $identityValue);
            $identityValue = str_replace('/', '', $identityValue);
            $explodeIdentity = explode('.', $identityValue);
            array_splice($explodeIdentity, -1, 1);

            $product->setGroupIdentity(implode('.', $explodeIdentity));
        }
    }
}