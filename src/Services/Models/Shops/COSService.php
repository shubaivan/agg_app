<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class COSService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     *
     * 0681866006007
     * 0681866006014
     * 0681866006008
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        if (strlen($sku) > 3) {
            $product->setGroupIdentity(mb_substr($sku, 0, -3));   
        }
    }
}