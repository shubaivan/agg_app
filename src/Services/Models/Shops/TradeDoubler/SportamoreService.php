<?php


namespace App\Services\Models\Shops\TradeDoubler;


use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;

class SportamoreService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
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
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $sku = $product->getSku();
        if ($sku) {
            $product->setGroupIdentity($sku);   
        }
    }
}