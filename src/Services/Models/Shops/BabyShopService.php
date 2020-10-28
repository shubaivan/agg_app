<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class BabyShopService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * productUrl: "https://www.babyshop.se/nate-lace-sneakers-green-nubuck/p/270322?country_override=SE"
     * productUrl: "https://www.babyshop.se/nate-lace-sneakers-green-nubuck/p/270322?country_override=SE"
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $productUrl = $product->getProductUrl();
        if (preg_match(
            '/p\/(.|\n*)+\?/',
            $productUrl,
            $match
        )) {
            $matchData = array_shift($match);
            $matchData = str_replace('p/', '', $matchData);
            $matchData = str_replace('?', '', $matchData);

            $product->setGroupIdentity($matchData);
        }

    }
}