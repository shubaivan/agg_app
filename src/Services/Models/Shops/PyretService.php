<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;

class PyretService implements IdentityGroup
{
    /**
     * @var array
     */
    private $identityBrand = [];

    /**
     * PyretService constructor.
     */
    public function __construct()
    {
        $this->identityBrand = $this->identityBrand();
    }

    public function identityGroupColumn(Product $product)
    {
        if (array_key_exists($product->getBrand(), $this->identityBrand)) {
            $strategy = $this->identityBrand[$product->getBrand()];
        } else {
            if (preg_match('/([^\/]+$)/', $product->getProductUrl(), $matches)) {
                $lastPartUrl = array_shift($matches);
                $explodeLastPartUrl = explode('-', $lastPartUrl);
                if (count($explodeLastPartUrl) > 2) {
                    $selectGroupIdentity = array_slice($explodeLastPartUrl, -2, 1);
                    $groupIdentity = array_shift($selectGroupIdentity);
                    $product->setGroupIdentity($groupIdentity);
                }
            }

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "Polarn O. Pyret" => new CutSomeDigitFromEan(-3)
        ];
    }
}