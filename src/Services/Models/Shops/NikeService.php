<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class NikeService extends AbstractShop
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

        $name = $product->getName();
        $product->setGroupIdentity($name);
    }
}