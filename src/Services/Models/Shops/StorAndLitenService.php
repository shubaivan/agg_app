<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class StorAndLitenService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $sku = $product->getSku();
        $cut = mb_substr($sku, 0, 4);
        $mb_strtolower = mb_strtolower($product->getBrand());
        $product->setGroupIdentity($cut.'_'.$mb_strtolower);
    }
}