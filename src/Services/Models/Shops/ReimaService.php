<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class ReimaService extends AbstractShop
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

        $explode = explode('-', $product->getSku());
        if (count($explode) > 0) {
            $groupIdentity = array_shift($explode);
            $product->setGroupIdentity($groupIdentity);
        } else {
            $groupIdentity = array_shift($explode);
            $product->setGroupIdentity($groupIdentity);
        }
    }
}