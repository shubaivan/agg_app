<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class SportshopenService implements IdentityGroup
{
    public function identityGroupColumn(Product $product)
    {
        $sku = $product->getSku();
        if ($sku && $product->getProductUrl()) {
            $explodeSku = explode('_', $sku);
            if ($explodeSku) {
                $firstEl = array_shift($explodeSku);
                $firstElStr = mb_substr($firstEl, 0, 4);
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
        if (!$product->getGroupIdentity()) {
            $product->setGroupIdentity($product->getSku());
        }
    }

}