<?php


namespace App\Services\Models\Shops\Awin;

use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutFirstSomeWordFromProductName;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutLastSomeWordFromProductName;
use App\Services\Models\Shops\Strategies\FullProductName;

class BlueTomatoService implements IdentityGroup
{
    private $identityBrand = [];

    /**
     * EllosSEService constructor.
     * @param array $identityBrand
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
            $strategy = new CutSomeDigitFromEan(-3);
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "Billabong" => new CutLastSomeDigitFromSku(-2),
            "Anon" => new CutSomeDigitFromEan(-3), "Armada" => new CutSomeDigitFromEan(-3), "Atomic" => new CutSomeDigitFromEan(-3), "Blind" => new CutSomeDigitFromEan(-3), "Blue Tomato" => new CutSomeDigitFromEan(10), "Body Glove" => new CutSomeDigitFromEan(-2), "Burton" => new CutSomeDigitFromEan(-3), "Capita" => new CutSomeDigitFromEan(-3), "Converse" => new CutSomeDigitFromEan(-3),
            "Dainese" => new FullProductName(), "Dalbello" => new CutSomeDigitFromEan(-3), "Dragon" => new CutSomeDigitFromEan(-3), "FOX" => new CutLastSomeWordFromProductName(1), "Faction" => new CutSomeDigitFromEan(-3), "Fjällräven" => new CutFirstSomeWordFromProductName(2), "Full Tilt" => new CutLastSomeDigitFromSku(-2), "Icetools" => new CutLastSomeDigitFromSku(-1),
            "K2" => new CutLastSomeDigitFromSku(-2), "KFD" => new CutSomeDigitFromEan(-3), "Kapten&Son" => new CutSomeDigitFromEan(-3), "Level" => new CutLastSomeDigitFromSku(-2), "Light" => new CutSomeDigitFromEan(-3), "Nitro" => new CutSomeDigitFromEan(-3), "Oakley" => new CutLastSomeDigitFromSku(-3), "Powell Peralta" => new CutSomeDigitFromEan(-3), "Quiksilver" => new CutLastSomeWordFromProductName(1),
            "REKD" => new CutLastSomeDigitFromSku(-1), "Ride" => new CutLastSomeDigitFromSku(-2), "Rip Curl" => new CutLastSomeDigitFromSku(-2), "Rome" => new CutLastSomeDigitFromSku(-2), "Salomon" => new CutSomeDigitFromEan(-2), "Santa Cruz" => new CutSomeDigitFromEan(-2), "Scott" => new CutLastSomeDigitFromSku(-2), "Smith" => new CutSomeDigitFromEan(-2), "Sweet Protection" => new CutLastSomeDigitFromSku(-3),
            "TOMS" => new CutLastSomeDigitFromSku(-2), "TSG" => new CutSomeDigitFromEan(-3), "The North Face" => new CutLastSomeDigitFromSku(-2), "Tricks" => new CutSomeDigitFromEan(-3), "Vans" => new CutLastSomeDigitFromSku(-2), "Volcom" => new CutSomeDigitFromEan(-3), "Völkl" => new CutLastSomeDigitFromSku(-2), "Zanier" => new CutSomeDigitFromEan(-2), "Zine" => new CutLastSomeDigitFromSku(-2)
        ];
    }
}