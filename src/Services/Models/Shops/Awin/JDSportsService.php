<?php

namespace App\Services\Models\Shops\Awin;

use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEanAndFullName;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndSomeFromEan;
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
            "ASICS" => new FullEan(), "Champion" => new FullEan(), "Converse" => new FullEan(), "Crocs" => new FullEan(), "Ellesse" => new FullEan(), "Emporio Armani EA7" => new FullEan(), "G-Form" => new FullEan(), "Guess" => new FullEan(), "Hummel" => new FullEan(), "ILLUSIVE LONDON" => new FullEan(), "Joma" => new FullEan(), "Kappa" => new FullEan(), "Kickers" => new FullEan(), "Macron" => new FullEan(), "Mizuno" => new FullEan(), "Nike" => new FullEan(), "Official Team" => new FullEan(), "Speedo" => new FullEan(), "Supply & Demand" => new FullEan(), "Umbro" => new FullEan(),
            "Adidas" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -1), "Berghaus" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -1),
            "Adidas Originals" => new CutLastSomeDigitFromSku(-2), "Sonneti"  => new CutLastSomeDigitFromSku(-2), "The North Face" => new CutLastSomeDigitFromSku(-2), "Vans" => new CutLastSomeDigitFromSku(-2), "Venum" => new CutLastSomeDigitFromSku(-2),
            "Fila" => new FullProductName(), "Lacoste" => new FullProductName(),
            "Levi's" => new FullProductName(),
            "Lyle & Scott" => new FullProductName(), "New Balance" => new FullProductName(), "Reebok" => new FullProductName(),
            "Jordan" => new CutSomeDigitFromEan(-3), "McKenzie" => new CutSomeDigitFromEan(-3),
            "Napapijri" => new CutSomeDigitFromEanAndFullName(-2), "New era" => new CutSomeDigitFromEanAndFullName(-2), "Puma" => new CutSomeDigitFromEanAndFullName(-2), "Shock Doctor" => new CutSomeDigitFromEanAndFullName(-2), "Timberland" => new CutSomeDigitFromEanAndFullName(-2),
            "Rascal" => new CutSomeDigitFromEanAndFullName(-3)
        ];
    }
}