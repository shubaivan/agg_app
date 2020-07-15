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
            $getMatchPartName = array_shift($explodeName);
        } else {
            $getMatchPartName = $name;
        }
        $groupIdentity = str_replace(' ', '_', mb_strtolower($getMatchPartName));
        $product->setGroupIdentity($groupIdentity);
    }
}