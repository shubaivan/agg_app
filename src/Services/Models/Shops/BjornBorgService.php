<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class BjornBorgService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * 2011-1117_70011-134-
     * 2011-1117_70011-158-
     * 2011-1117_70011-146-
     * 2011-1117_70011-170
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $sku = $product->getSku();
        $trim = trim($sku, '-');
        $preg_split = preg_split('/-|_/', $trim);
        if (count($preg_split)) {
            $array_slice = array_slice($preg_split, 0, count($preg_split) - 1);
            $implode = implode('_', $array_slice);
            $product->setGroupIdentity($implode);
        }
    }
}