<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class BabyLandService extends AbstractShop implements IdentityGroup
{
    /**
     * name: "Color Kids Mössa & Vantar (Candy pink) (48)"
     * sku: "207126"
     * sku: "207127"
     *
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $groupIdentity = mb_substr($sku, 0, 5);
        $product->setGroupIdentity($groupIdentity);

        $name = $product->getName();
        preg_match_all("/\([^)]+\)/", $name, $m);
        if ($m > 0) {
            $extras = array_shift($m);
            if (isset($extras[0])) {
                $this->analysisColorValue($extras[0], $product);
            }
            if (isset($extras[1])) {
                $size = $extras[1];
                $size = str_replace('(', '', $size);
                $size = str_replace(')', '', $size);
                $product->setSeparateExtra(Product::SIZE, $size);
            }
        }
    }
}