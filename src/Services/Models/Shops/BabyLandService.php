<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class BabyLandService implements IdentityGroup
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
                $product->setSeparateExtra('COLOR', $extras[0]);
            }
            if (isset($extras[1])) {
                $product->setSeparateExtra('SIZE', $extras[1]);
            }
        }
    }
}