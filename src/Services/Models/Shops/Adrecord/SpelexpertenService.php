<?php

namespace App\Services\Models\Shops\Adrecord;

use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeWordFromProductName;
use App\Services\Models\Shops\Strategies\CutTheRestOfProductNameAfterSymbol;

class SpelexpertenService implements IdentityGroup
{
    /**
     * @var array
     */
    private $identityBrand = [];

    /**
     * SpelexpertenService constructor.
     */
    public function __construct()
    {
        $this->identityBrand = $this->identityBrand();
    }

    /**
     * @param Product $product
     * @return Product|mixed
     */
    public function identityGroupColumn(Product $product)
    {
        if (array_key_exists($product->getBrand(), $this->identityBrand)) {
            $strategy = $this->identityBrand[$product->getBrand()];
        } else {
            $sku = $product->getSku();
            $product->setGroupIdentity($sku);

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "Cards Against Humanity" => new CutTheRestOfProductNameAfterSymbol(1, ':'), "Gen 42" => new CutTheRestOfProductNameAfterSymbol(1, ':'),
            "Bulls" => new CutTheRestOfProductNameAfterSymbol(1, '-'), "Oakie Doakie" => new CutTheRestOfProductNameAfterSymbol(1, '-'), "Sweets kendamas" => new CutTheRestOfProductNameAfterSymbol(1,'-'),
            "Active Kendama" => new CutSomeWordFromProductName(1), "KROM" => new CutSomeWordFromProductName(1), "Czech Games Edition" => new CutSomeWordFromProductName(1),
            "GameGenic" => new CutSomeWordFromProductName(2),
            "Gigantoskop" => new CutTheRestOfProductNameAfterSymbol(1,     '(')
        ];
    }
}