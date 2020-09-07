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
        $identity = [];
        $sku = $product->getSku();
        if (strlen($sku)) {
            $cut = mb_substr($sku, 0, -3);
            $identity[]= preg_replace('/' . $cut . '/', '', $sku);

        }
        $ean = $product->getEan();
        if (strlen($ean)) {
            $cut = mb_substr($ean, 0, -4);
            $identity[]= preg_replace('/' . $cut . '/', '', $ean);
        }
        if (count($identity)) {
            $product->setGroupIdentity(implode('_', $identity));
        } else {
            $product->setGroupIdentity($product->getIdentityUniqData());
        }
    }
}