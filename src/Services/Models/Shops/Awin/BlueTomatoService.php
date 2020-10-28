<?php


namespace App\Services\Models\Shops\Awin;

use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutSomeWordsFromProductNameByDelimiter;
use App\Services\Models\Shops\Strategies\FullProductName;

class BlueTomatoService extends AbstractShop
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
            $strategy = new CutSomeDigitFromEan(-3);
        }

        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "Billabong" => new CutSomeDigitFromSku(-2),
            "Anon" => new CutSomeDigitFromEan(-3), "Armada" => new CutSomeDigitFromEan(-3), "Atomic" => new CutSomeDigitFromEan(-3), "Blind" => new CutSomeDigitFromEan(-3), "Blue Tomato" => new CutSomeDigitFromEan(10), "Body Glove" => new CutSomeDigitFromEan(-2), "Burton" => new CutSomeDigitFromEan(-3), "Capita" => new CutSomeDigitFromEan(-3), "Converse" => new CutSomeDigitFromEan(-3),
            "Dainese" => new FullProductName(), "Dalbello" => new CutSomeDigitFromEan(-3), "Dragon" => new CutSomeDigitFromEan(-3), "FOX" => new CutSomeWordsFromProductNameByDelimiter(1), "Faction" => new CutSomeDigitFromEan(-3), "Fjällräven" => new CutSomeWordsFromProductNameByDelimiter(2), "Full Tilt" => new CutSomeDigitFromSku(-2), "Icetools" => new CutSomeDigitFromSku(-1),
            "K2" => new CutSomeDigitFromSku(-2), "KFD" => new CutSomeDigitFromEan(-3), "Kapten&Son" => new CutSomeDigitFromEan(-3), "Level" => new CutSomeDigitFromSku(-2), "Light" => new CutSomeDigitFromEan(-3), "Nitro" => new CutSomeDigitFromEan(-3), "Oakley" => new CutSomeDigitFromSku(-3), "Powell Peralta" => new CutSomeDigitFromEan(-3), "Quiksilver" => new CutSomeWordsFromProductNameByDelimiter(1),
            "REKD" => new CutSomeDigitFromSku(-1), "Ride" => new CutSomeDigitFromSku(-2), "Rip Curl" => new CutSomeDigitFromSku(-2), "Rome" => new CutSomeDigitFromSku(-2), "Salomon" => new CutSomeDigitFromEan(-2), "Santa Cruz" => new CutSomeDigitFromEan(-2), "Scott" => new CutSomeDigitFromSku(-2), "Smith" => new CutSomeDigitFromEan(-2), "Sweet Protection" => new CutSomeDigitFromSku(-3),
            "TOMS" => new CutSomeDigitFromSku(-2), "TSG" => new CutSomeDigitFromEan(-3), "The North Face" => new CutSomeDigitFromSku(-2), "Tricks" => new CutSomeDigitFromEan(-3), "Vans" => new CutSomeDigitFromSku(-2), "Volcom" => new CutSomeDigitFromEan(-3), "Völkl" => new CutSomeDigitFromSku(-2), "Zanier" => new CutSomeDigitFromEan(-2), "Zine" => new CutSomeDigitFromSku(-2)
        ];
    }
}