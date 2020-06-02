<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class ElodiService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $name = $product->getName();
        $explodeName = explode(' - ', $name);
        if (count($explodeName) > 1) {
            $groupIdentity = str_replace(' ', '_', mb_strtolower(array_shift($explodeName)));
            $product->setGroupIdentity($groupIdentity);
        }
    }
}