<?php

namespace App\Services\Models\Shops\Awin;

use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEanAndFullName;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndSomeFromEan;
use App\Services\Models\Shops\Strategies\CutSomeWordsFromProductNameByDelimiter;
use App\Services\Models\Shops\Strategies\FullProductName;

class JDSportsService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     * @throws \Exception
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
            $strategy = new CutSomeDigitFromSkuAndSomeFromEan(
                6, 9
            );
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "ASICS" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Champion" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Converse" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Crocs" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Ellesse" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Emporio Armani EA7" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "G-Form" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Guess" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Hummel" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "ILLUSIVE LONDON" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Joma" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Kappa" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Kickers" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Macron" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Mizuno" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Nike" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Official Team" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Speedo" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Supply & Demand" => new CutSomeWordsFromProductNameByDelimiter(1, ','), "Umbro" => new CutSomeWordsFromProductNameByDelimiter(1, ','),
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