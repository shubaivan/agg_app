<?php


namespace App\Services\Models\Shops\TradeDoubler;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class SportamoreService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     *
     * 03203
     * 03203
     * 03203
     * 03203
     * 03203
     * 041641
     * 041641
     * 041641
     * 041641
     * 041641
     * 041641
     * 041641
     * 041641
     * 041641
     * 041653
     * 041653
     * 041653
     * 041661
     * 041661
     * 041661
     * 041661
     * 041661
     * 041661
     */
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        if ($sku) {
            $product->setGroupIdentity($sku);   
        }
    }
}