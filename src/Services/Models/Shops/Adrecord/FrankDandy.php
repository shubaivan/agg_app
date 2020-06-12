<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class FrankDandy implements IdentityGroup
{
    /**
     * 1/se-p6001-v7942-s31741
     * 1/se-p5993-v7931-s31717
     * 1/se-p5992-v7930-s31715
     * 1/se-p5991-v7929-s31713
     * 1/se-p5990-v7928-s31711
     * 1/se-p5989-v7927-s31709
     * 1/se-p5988-v7926-s31707
     * 1/se-p5987-v7925-s31705
     *
     *
     * 1/se-p2372-v3925-s13125
     * 1/se-p2371-v3924-s13123
     *
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        $explodeSku = explode('-', $sku);
        if ($explodeSku) {
            $lastEl = array_pop($explodeSku);
            $identity = mb_substr($lastEl, 0, 4);
            $product->setGroupIdentity($identity);
        }
    }
}