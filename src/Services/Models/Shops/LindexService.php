<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndFullName;

class LindexService extends AbstractShop
{
    /**
     * @param Product $product
     * @return Product|bool|mixed|void
     * @throws \ReflectionException
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        if (array_key_exists($product->getBrand(), $this->identityBrand)) {
            $strategy = $this->identityBrand[$product->getBrand()];
        } else {
            $lastPartUrl = preg_match("/[^\/]+$/", $product->getProductUrl(), $m);
            if ($lastPartUrl > 0) {
                $identityPart = array_shift($m);
                $explodeLastPartUrl = explode('-', $identityPart);
                $groupIdentity = array_shift($explodeLastPartUrl);
                $product->setGroupIdentity($groupIdentity);
            }

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "Lindex" => new CutSomeDigitFromSkuAndFullName(-2)
        ];
    }
}