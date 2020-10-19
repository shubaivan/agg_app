<?php

namespace App\Services\Models\Shops\Awin;

use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEanAndFullName;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndSomeFromEan;
use App\Services\Models\Shops\Strategies\CutTheRestOfProductNameAfterSymbol;
use App\Services\Models\Shops\Strategies\FullEan;
use App\Services\Models\Shops\Strategies\FullProductName;

class JDSportsService implements IdentityGroup
{
    private $identityBrand = [];

    /**
     * JDSportsService constructor.
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
            $strategy = new CutSomeDigitFromSkuAndSomeFromEan(
                6, 9
            );
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "ASICS" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Champion" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Converse" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Crocs" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Ellesse" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Emporio Armani EA7" => new CutTheRestOfProductNameAfterSymbol(1, ','), "G-Form" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Guess" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Hummel" => new CutTheRestOfProductNameAfterSymbol(1, ','), "ILLUSIVE LONDON" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Joma" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Kappa" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Kickers" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Macron" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Mizuno" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Nike" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Official Team" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Speedo" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Supply & Demand" => new CutTheRestOfProductNameAfterSymbol(1, ','), "Umbro" => new CutTheRestOfProductNameAfterSymbol(1, ','),
            "Adidas" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -1), "Berghaus" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -1),
            "Adidas Originals" => new CutSomeDigitFromSku(-2), "Sonneti"  => new CutSomeDigitFromSku(-2), "The North Face" => new CutSomeDigitFromSku(-2), "Vans" => new CutSomeDigitFromSku(-2), "Venum" => new CutSomeDigitFromSku(-2),
            "Fila" => new FullProductName(), "Lacoste" => new FullProductName(),
            "Levi's" => new FullProductName(),
            "Lyle & Scott" => new FullProductName(), "New Balance" => new FullProductName(), "Reebok" => new FullProductName(),
            "Jordan" => new CutSomeDigitFromEan(-3), "McKenzie" => new CutSomeDigitFromEan(-3),
            "Napapijri" => new CutSomeDigitFromEanAndFullName(-2), "New era" => new CutSomeDigitFromEanAndFullName(-2), "Puma" => new CutSomeDigitFromEanAndFullName(-2), "Shock Doctor" => new CutSomeDigitFromEanAndFullName(-2), "Timberland" => new CutSomeDigitFromEanAndFullName(-2),
            "Rascal" => new CutSomeDigitFromEanAndFullName(-3)
        ];
    }
}