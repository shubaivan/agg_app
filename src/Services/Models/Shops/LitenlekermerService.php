<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;

class LitenlekermerService extends AbstractShop
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
        if (count($explode) > 2) {
            $firstPartIdentity = array_shift($explode);
            $secondPartIdentity = array_shift($explode);
            $product->setGroupIdentity($firstPartIdentity . $secondPartIdentity);
        } else {
            $groupIdentity = array_shift($explode);
            $product->setGroupIdentity($groupIdentity);
        }
    }
}