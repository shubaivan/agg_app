<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class LekiaService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     *
     * alga_38010693
     * alga_38010694
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        $sku = $product->getSku();

        $preg_split = preg_split('/-/', $sku);
        if (count($preg_split) > 1) {
            $sku = array_shift($preg_split);
        }

        $groupIdentity = mb_substr($sku, 0, -2);
        if (strlen($groupIdentity)) {
            $product->setGroupIdentity($groupIdentity);
        }
    }
}