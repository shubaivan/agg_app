<?php

namespace App\Services\Models\Shops\Adrecord;

use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeWordsFromProductNameByDelimiter;

class SpelexpertenService extends AbstractShop
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
            $sku = $product->getSku();
            $product->setGroupIdentity($sku);

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "Cards Against Humanity" => new CutSomeWordsFromProductNameByDelimiter(1, ':'), "Gen 42" => new CutSomeWordsFromProductNameByDelimiter(1, ':'),
            "Bulls" => new CutSomeWordsFromProductNameByDelimiter(1, '-'), "Oakie Doakie" => new CutSomeWordsFromProductNameByDelimiter(1, '-'), "Sweets kendamas" => new CutSomeWordsFromProductNameByDelimiter(1,'-'),
            "Active Kendama" => new CutSomeWordsFromProductNameByDelimiter(1), "KROM" => new CutSomeWordsFromProductNameByDelimiter(1), "Czech Games Edition" => new CutSomeWordsFromProductNameByDelimiter(1),
            "GameGenic" => new CutSomeWordsFromProductNameByDelimiter(2),
            "Gigantoskop" => new CutSomeWordsFromProductNameByDelimiter(1,'(')
        ];
    }
}