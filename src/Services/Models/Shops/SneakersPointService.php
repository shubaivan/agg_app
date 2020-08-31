<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class SneakersPointService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     *
     * 258908101101
     * 258908101102
     * 258908101103
     * 258908101104
     * 258908101105
     * 258908101106
     * 258908101107
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $cut = substr($sku, -3);
        $gi = preg_replace('/' . $cut . '/', '', $sku);
        $product->setGroupIdentity($gi);
    }
}