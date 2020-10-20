<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutSomeWordFromProductName;
use App\Services\Models\Shops\Strategies\CutSomeBlocksByDelimiterFromSku;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutTheRestOfProductNameAfterSymbol;

class LitenlekerService implements IdentityGroup
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
            $productUrl = $product->getProductUrl();
            $preg_match = preg_match(
                '/([^\/]+$)/',
                $productUrl,
                $matches);
            if ($preg_match) {
                $result = array_shift($matches);
                if ($result) {
                    $implode = explode('-', $result);
                    if (count($implode) >= 3) {
                        $array_slice = array_slice($implode, 0, 3);
                        $product->setGroupIdentity(implode('-', $array_slice));
                    }
                }
            }

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "AluSport" => new CutTheRestOfProductNameAfterSymbol(2, '-'), "Animal Riding" => new CutTheRestOfProductNameAfterSymbol(2, '-'), "Ferrari" => new CutTheRestOfProductNameAfterSymbol(2, '-'), "Mikka of Scandinavia"  => new CutTheRestOfProductNameAfterSymbol(2, '-'),
            "Baghera" => new CutSomeDigitFromEan(-2), "BiBaBad" => new CutSomeDigitFromEan(-2), "Britton" => new CutSomeDigitFromEan(-2), "Den Goda Fen" => new CutSomeDigitFromEan(-2), "EuroToys" => new CutSomeDigitFromEan(-2), "Levenhuk" => new CutSomeDigitFromEan(-2), "PQP" => new CutSomeDigitFromEan(-2), "Troll" => new CutSomeDigitFromEan(-2),
            "Bandito Sport" => new CutSomeDigitFromSku(-1), "Kaxholmen" => new CutSomeDigitFromSku(-1), "Minisa" => new CutSomeDigitFromSku(-1), "My Hood" => new CutSomeDigitFromSku(-1), "Pellianni" => new CutSomeDigitFromSku(-1), "SportMe" => new CutSomeDigitFromSku(-1), "Strider" => new CutSomeDigitFromSku(-1), "Sunny" => new CutSomeDigitFromSku(-1), "Swimpy" => new CutSomeDigitFromSku(-1), "WinSport" => new CutSomeDigitFromSku(-1),
            "ByASTRUP" => new CutSomeDigitFromEan(-3), "Pippi Långstrump" => new CutSomeDigitFromEan(-3), "Twistshake" => new CutSomeDigitFromEan(-3),
            "Coolslide" => new CutSomeWordFromProductName(1, '-'), "Hauck" => new CutSomeWordFromProductName(1, '-'), "Homestyle4u" => new CutSomeWordFromProductName(1, '-'), "Hörby Bruk" => new CutSomeWordFromProductName(1, '-'),
            "KOBI" => new CutSomeBlocksByDelimiterFromSku(2, '-'),
            "Kids Concept" => new CutSomeWordFromProductName(1, '-'),
            "La Siesta"  => new CutSomeWordFromProductName(-1, '-'), "Leklyckan" => new CutSomeWordFromProductName(-1, '-'),
            "Liix" => new CutSomeWordFromProductName(3), "Manis-h" => new CutSomeWordFromProductName(3), "Meow Baby" => new CutSomeWordFromProductName(3),
            "Miffy" => new CutSomeWordFromProductName(1), "Pufz" => new CutSomeWordFromProductName(1), "Safari" => new CutSomeWordFromProductName(1), "Scrunch" => new CutSomeWordFromProductName(1), "Skruttenringen" => new CutSomeWordFromProductName(1),
            "Mitrotrading" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "NG Baby" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "NORWOOD DENMARK" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Timbela" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Wigglekart" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Yipeeh" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Övrigt Lek" => new CutSomeBlocksByDelimiterFromSku(1, '-'),
            "Playsam" => new CutSomeWordFromProductName(-2),
            "STIGA" => new CutSomeDigitFromEan(-4),
            "SunSport" => new CutSomeDigitFromSku(-2), "Sunsport" => new CutSomeDigitFromSku(-2), "WaterHero" => new CutSomeDigitFromSku(-2),
        ];
    }
}