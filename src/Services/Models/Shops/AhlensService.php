<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\FullProductName;

class AhlensService implements IdentityGroup
{
    /**
     * @var array
     */
    private $identityBrand = [];

    /**
     * AhlensService constructor.
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
            $productUrl = $product->getProductUrl();
            $lastChar = mb_substr($productUrl, -1);
            if ($lastChar == '/') {
                $productUrl = mb_substr($productUrl, 0, -1);
            }
            $productUrl = preg_replace("/[^\/]+$/", '', $productUrl);
            $lastChar = mb_substr($productUrl, -1);
            while ($lastChar == '/') {
                $productUrl = mb_substr($productUrl, 0, -1);
                $lastChar = mb_substr($productUrl, -1);
            }

            if (preg_match("/[^\/]+$/", $productUrl, $matches) > 0) {
                $sku = $product->getSku();
                $skuId = mb_substr($sku, 0, 3);
                $product->setGroupIdentity($skuId . array_shift($matches));
            }

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "ALFONS ÅBERG" => new CutLastSomeDigitFromSku(-2), "Birkenstock" => new CutLastSomeDigitFromSku(-2), "Blafre" => new CutLastSomeDigitFromSku(-2), "Bloomingville" => new CutLastSomeDigitFromSku(-2), "Bumbo" => new CutLastSomeDigitFromSku(-2), "CTH" => new CutLastSomeDigitFromSku(-2), "Creamie" => new CutLastSomeDigitFromSku(-2), "Design House Stockholm" => new CutLastSomeDigitFromSku(-2), "Design Letters" => new CutLastSomeDigitFromSku(-2), "Diesel" => new CutLastSomeDigitFromSku(-2), "ERNST" => new CutLastSomeDigitFromSku(-2), "Fila" => new CutLastSomeDigitFromSku(-2), "Fiskars" => new CutLastSomeDigitFromSku(-2), "GAP" => new CutLastSomeDigitFromSku(-2), "Gant" => new CutLastSomeDigitFromSku(-2), "Hestra" => new CutLastSomeDigitFromSku(-2), "Hummel" => new CutLastSomeDigitFromSku(-2), "Iris hantverk" => new CutLastSomeDigitFromSku(-2), "LMTD" => new CutLastSomeDigitFromSku(-2), "Le Creuset" => new CutLastSomeDigitFromSku(-2), "Loccitane" => new CutLastSomeDigitFromSku(-2), "M Lindberg" => new CutLastSomeDigitFromSku(-2), "Mads Nørgaard" => new CutLastSomeDigitFromSku(-2), "NYX Professional Makeup" => new CutLastSomeDigitFromSku(-2), "Nicotext" => new CutLastSomeDigitFromSku(-2), "PIPPI"  => new CutLastSomeDigitFromSku(-2),
            "BABYBJÖRN" => new FullProductName(), "Name it" => new FullProductName()
        ];
    }
}