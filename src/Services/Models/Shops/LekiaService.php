<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class LekiaService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     * 
     * alga_38010693
     * alga_38010694
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $cut = substr($sku, -2);
        $gi = preg_replace('/' . $cut . '/', '', $sku);
        $product->setGroupIdentity($gi);
    }
}