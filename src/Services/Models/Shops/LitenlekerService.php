<?php

namespace App\Services\Models\Shops;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutSomeWordsFromProductNameByDelimiter;
use App\Services\Models\Shops\Strategies\CutSomeBlocksByDelimiterFromSku;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;

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
            "AluSport" => new CutSomeWordsFromProductNameByDelimiter(2, '-'), "Animal Riding" => new CutSomeWordsFromProductNameByDelimiter(2, '-'), "Ferrari" => new CutSomeWordsFromProductNameByDelimiter(2, '-'), "Mikka of Scandinavia"  => new CutSomeWordsFromProductNameByDelimiter(2, '-'),
            "Baghera" => new CutSomeDigitFromEan(-2), "BiBaBad" => new CutSomeDigitFromEan(-2), "Britton" => new CutSomeDigitFromEan(-2), "Den Goda Fen" => new CutSomeDigitFromEan(-2), "EuroToys" => new CutSomeDigitFromEan(-2), "Levenhuk" => new CutSomeDigitFromEan(-2), "PQP" => new CutSomeDigitFromEan(-2), "Troll" => new CutSomeDigitFromEan(-2),
            "Bandito Sport" => new CutSomeDigitFromSku(-1), "Kaxholmen" => new CutSomeDigitFromSku(-1), "Minisa" => new CutSomeDigitFromSku(-1), "My Hood" => new CutSomeDigitFromSku(-1), "Pellianni" => new CutSomeDigitFromSku(-1), "SportMe" => new CutSomeDigitFromSku(-1), "Strider" => new CutSomeDigitFromSku(-1), "Sunny" => new CutSomeDigitFromSku(-1), "Swimpy" => new CutSomeDigitFromSku(-1), "WinSport" => new CutSomeDigitFromSku(-1),
            "ByASTRUP" => new CutSomeDigitFromEan(-3), "Pippi Långstrump" => new CutSomeDigitFromEan(-3), "Twistshake" => new CutSomeDigitFromEan(-3),
            "Coolslide" => new CutSomeWordsFromProductNameByDelimiter(1, '-'), "Hauck" => new CutSomeWordsFromProductNameByDelimiter(1, '-'), "Homestyle4u" => new CutSomeWordsFromProductNameByDelimiter(1, '-'), "Hörby Bruk" => new CutSomeWordsFromProductNameByDelimiter(1, '-'),
            "KOBI" => new CutSomeBlocksByDelimiterFromSku(2, '-'),
            "Kids Concept" => new CutSomeWordsFromProductNameByDelimiter(1, '-'),
            "La Siesta"  => new CutSomeWordsFromProductNameByDelimiter(-1, '-'), "Leklyckan" => new CutSomeWordsFromProductNameByDelimiter(-1, '-'),
            "Liix" => new CutSomeWordsFromProductNameByDelimiter(3), "Manis-h" => new CutSomeWordsFromProductNameByDelimiter(3), "Meow Baby" => new CutSomeWordsFromProductNameByDelimiter(3),
            "Miffy" => new CutSomeWordsFromProductNameByDelimiter(1), "Pufz" => new CutSomeWordsFromProductNameByDelimiter(1), "Safari" => new CutSomeWordsFromProductNameByDelimiter(1), "Scrunch" => new CutSomeWordsFromProductNameByDelimiter(1), "Skruttenringen" => new CutSomeWordsFromProductNameByDelimiter(1),
            "Mitrotrading" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "NG Baby" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "NORWOOD DENMARK" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Timbela" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Wigglekart" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Yipeeh" => new CutSomeBlocksByDelimiterFromSku(1, '-'), "Övrigt Lek" => new CutSomeBlocksByDelimiterFromSku(1, '-'),
            "Playsam" => new CutSomeWordsFromProductNameByDelimiter(-2),
            "STIGA" => new CutSomeDigitFromEan(-4),
            "SunSport" => new CutSomeDigitFromSku(-2), "Sunsport" => new CutSomeDigitFromSku(-2), "WaterHero" => new CutSomeDigitFromSku(-2),
        ];
    }
}