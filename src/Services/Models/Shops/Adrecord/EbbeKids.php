<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class EbbeKids implements IdentityGroup
{
    /**
     * 10713400023004
     * 10713400022004
     *
     * 0002070099080
     * 0002070070080
     *
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $identity = mb_substr($sku, 0, 8);
        $product->setGroupIdentity($identity);
    }
}