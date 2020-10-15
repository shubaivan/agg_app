<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;
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
            "Baghera", "BiBaBad", "Britton", "Den Goda Fen", "EuroToys", "Levenhuk", "PQP", "Troll"
        ];
    }
}