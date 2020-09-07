<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class BabyBjornService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     *
     * sku
     * BS_005083
     * BS_005045
     * BS_005076
     * BS_005061
     * BS_005026
     * BS_005089
     * BS_005084
     * BS_005008
     * BS_005024
     * BS_005029
     * BS_005022
     * BS_005003
     * BS_006014
     * BS_006021
     * BS_006013
     * BS_006018
     * BS_006020
     * BS_006043
     * BS_006041
     * BS_006003
     * BS_006015
     * BS_006001
     * BS_006017
     * BS_006016
     *
     * ean
     * 005083
     * 005045
     * 005076
     * 005061
     * 005026
     * 005089
     * 005084
     * 005008
     * 005024
     * 005029
     * 005022
     * 005003
     * 006014
     * 006021
     * 006013
     * 006018
     * 006020
     * 006043
     * 006041
     * 006003
     * 006015
     * 006001
     * 006017
     * 006016
     */
    public function identityGroupColumn(Product $product)
    {
        $identity = [];
        $sku = $product->getSku();
        if (strlen($sku)) {
            $cut = mb_substr($sku, 0, -2);
            $identity[]= preg_replace('/' . $cut . '/', '', $sku);
            
        }
        $ean = $product->getEan();
        if (strlen($ean)) {
            $cut = mb_substr($ean, 0, -2);
            $identity[]= preg_replace('/' . $cut . '/', '', $ean);
        }
        if (count($identity)) {
            $product->setGroupIdentity(implode('_', $identity));
        } else {
            $product->setGroupIdentity($product->getIdentityUniqData());
        }
    }
}