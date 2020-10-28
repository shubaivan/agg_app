<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class LekmerService extends AbstractShop
{

    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * productUrl: "https://lekmer.se/penguin-vinteroverall-gra/p/36019?country_override=SE&dfw_tracker=42152-174894"
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