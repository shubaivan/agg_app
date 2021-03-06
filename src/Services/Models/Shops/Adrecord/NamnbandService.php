<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;

class NamnbandService extends AbstractShop
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

        $sku = $product->getSku();
        if ($sku && $product->getProductUrl()) {
            $firstEl = mb_substr($sku, 0, 2);
            $parts = parse_url($product->getProductUrl());
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (isset($query['url'])) {
                    if (preg_match('/([^\/]+$)/', $query['url'], $matches)) {
                        $identity = array_shift($matches);
                        $identityExplode = explode('-', $identity);
                        if ($identityExplode) {
                            $lastEl = array_shift($identityExplode);
                            $product->setGroupIdentity($firstEl . '_' . $lastEl);
                        }
                    }
                }
            }
        }
    }
}