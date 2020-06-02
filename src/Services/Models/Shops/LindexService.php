<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class LindexService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $lastPartUrl = preg_match("/[^\/]+$/", $product->getProductUrl(), $m);
        if ($lastPartUrl > 0) {
            $identityPart = array_shift($m);
            $explodeLastPartUrl = explode('-', $identityPart);
            $groupIdentity = array_shift($explodeLastPartUrl);
            $product->setGroupIdentity($groupIdentity);
        }
    }
}