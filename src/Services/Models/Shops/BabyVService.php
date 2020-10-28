<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class BabyVService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * productUrl: "https://www.babyv.se/sv/articles/1.37.18/babybjorn-babybjorn-balance-bliss-mesh-antracitgra"
     * productUrl: "https://www.babyv.se/sv/articles/1.37.9/babybjorn-babybjorn-balance-soft-jersey-morkgragra"
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

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

        if ($product->getName()) {
            $explodeName = explode(',', $product->getName());
            if (count($explodeName) > 1) {
                $color = array_pop($explodeName);
                $this->analysisColorValue($color, $product);
            }
        }
    }
}