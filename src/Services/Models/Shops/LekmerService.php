<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class LekmerService implements IdentityGroup
{

    /**
     * productUrl: "https://lekmer.se/penguin-vinteroverall-gra/p/36019?country_override=SE&dfw_tracker=42152-174894"
     *
     * @param Product $product
     */
    public function identityGroupColumn(Product $product)
    {
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