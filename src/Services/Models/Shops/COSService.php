<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class COSService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * 0681866006007
     * 0681866006014
     * 0681866006008
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $sku = $product->getSku();
        if (strlen($sku) > 3) {
            $product->setGroupIdentity(mb_substr($sku, 0, -3));   
        }
    }
}