<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class SneakersPointService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
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
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $identity = [];
        $sku = $product->getSku();
        if (strlen($sku)) {
            $identity[]= mb_substr($sku, 0, -3);

        }
        $ean = $product->getEan();
        if (strlen($ean)) {
            $identity[]= mb_substr($ean, 0, -4);
        }
        if (count($identity)) {
            $product->setGroupIdentity(implode('_', $identity));
        }
    }
}