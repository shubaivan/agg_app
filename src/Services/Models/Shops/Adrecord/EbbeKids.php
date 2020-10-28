<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;

class EbbeKids extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * 10713400023004
     * 10713400022004
     *
     * 0002070099080
     * 0002070070080
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $sku = $product->getSku();
        $identity = mb_substr($sku, 0, 7);
        $product->setGroupIdentity($identity);
    }
}