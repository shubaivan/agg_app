<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;

class ShirtstoreService extends AbstractShop
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
            $explodeSku = explode('-', $sku);
            if ($explodeSku) {
                $firstEl[] = array_shift($explodeSku);
                if (count($explodeSku) > 1) {
                    $firstEl[] = array_shift($explodeSku);
                }
                $firstElStr = implode('-', $firstEl);
                $parts = parse_url($product->getProductUrl());
                if (isset($parts['query'])) {
                    parse_str($parts['query'], $query);
                    if (isset($query['url'])) {
                        if (preg_match('/([^\/]+$)/', $query['url'], $matches)) {
                            $identity = array_shift($matches);
                            $identityExplode = explode('-', $identity);
                            if ($identityExplode) {
                                $lastEl = array_shift($identityExplode);
                                $product->setGroupIdentity($firstElStr . '_' . $lastEl);
                            }
                        }
                    }
                }
            }
        }
    }
}