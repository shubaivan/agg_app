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
     * ean: 3760216361391, sku: 257028
     * ean: 3760216361407, sku: 257029
     * ean: 3760216361377, sku: 257027
     * ean: 3760216360998, sku: 257026
     * ean: 3760216360981, sku: 257025
     *
     * ean: 6438429261612, sku: 257020
     *
     * ean: 6438429169260, sku: 256904
     * ean: 6438429169178, sku: 256900
     *
     *
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $groupIdentity = [];
        if (strlen($sku)) {
            $groupIdentity[] = mb_substr($sku, 0, 5);
        }

        $ean = $product->getEan();
        if (strlen($ean)) {
            $groupIdentity[] = mb_substr($ean, 0, -4);
        }

        if (count($groupIdentity)) {
            $product->setGroupIdentity(implode('_', $groupIdentity));
        }

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