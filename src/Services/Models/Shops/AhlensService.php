<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSku;
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
            "ALFONS ÅBERG" => new CutSomeDigitFromSku(-2), "Birkenstock" => new CutSomeDigitFromSku(-2), "Blafre" => new CutSomeDigitFromSku(-2), "Bloomingville" => new CutSomeDigitFromSku(-2), "Bumbo" => new CutSomeDigitFromSku(-2), "CTH" => new CutSomeDigitFromSku(-2), "Creamie" => new CutSomeDigitFromSku(-2), "Design House Stockholm" => new CutSomeDigitFromSku(-2), "Design Letters" => new CutSomeDigitFromSku(-2), "Diesel" => new CutSomeDigitFromSku(-2), "ERNST" => new CutSomeDigitFromSku(-2), "Fila" => new CutSomeDigitFromSku(-2), "Fiskars" => new CutSomeDigitFromSku(-2), "GAP" => new CutSomeDigitFromSku(-2), "Gant" => new CutSomeDigitFromSku(-2), "Hestra" => new CutSomeDigitFromSku(-2), "Hummel" => new CutSomeDigitFromSku(-2), "Iris hantverk" => new CutSomeDigitFromSku(-2), "LMTD" => new CutSomeDigitFromSku(-2), "Le Creuset" => new CutSomeDigitFromSku(-2), "Loccitane" => new CutSomeDigitFromSku(-2), "M Lindberg" => new CutSomeDigitFromSku(-2), "Mads Nørgaard" => new CutSomeDigitFromSku(-2), "NYX Professional Makeup" => new CutSomeDigitFromSku(-2), "Nicotext" => new CutSomeDigitFromSku(-2), "PIPPI"  => new CutSomeDigitFromSku(-2),
            "BABYBJÖRN" => new FullProductName(), "Name it" => new FullProductName(),
            "Adidas Originals" => new FullProductName(), "Braun" => new FullProductName(), "CarloBaby" => new FullProductName(), "Didriksons" => new FullProductName(), "Done by Deer" => new FullProductName(), "Pick&pack" => new FullProductName(), "Shepherd" => new FullProductName(), "Sorel" => new FullProductName(),
            "Cink" => new FullProductName(), "Geggamoja" => new FullProductName(),
            "Klippan" => new FullProductName(), "MICKI" => new FullProductName(), "Peak Performance" => new FullProductName(), "Save the Duck" => new FullProductName(), "Skip Hop" => new FullProductName(), "Summerville organic" => new FullProductName(), "TEDDYKOMPANIET" => new FullProductName()
        ];
    }
}