<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class ElodiService extends AbstractShop
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