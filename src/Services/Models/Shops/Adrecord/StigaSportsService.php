<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutTheRestOfProductNameAfterSymbol;

class StigaSportsService implements IdentityGroup
{
    /**
     * @var bool
     */
    private $match = false;

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
            $this->hyphenRule($product);
            $this->dotRule($product);

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "STIGA" => new CutSomeDigitFromEan(-2)
        ];
    }

    public function hyphenRule(Product $product)
    {
        if ($this->match) {
            return;
        }
        $sku = $product->getSku();
        $explodeSku = explode('-', $sku);
        if (count($explodeSku) >= 3) {
            $first = array_shift($explodeSku);
            $last = array_pop($explodeSku);
            $identity = $first . '_' . $last;
            $this->match = true;

            $product->setGroupIdentity($identity);
        }
    }

    public function dotRule(Product $product)
    {
        if ($this->match) {
            return;
        }
        $sku = $product->getSku();
        $explodeSku = explode('.', $sku);
        if (count($explodeSku) >= 3) {
            $first = array_shift($explodeSku);
            $last = array_pop($explodeSku);
            $identity = $first . '.' . $last;
            $this->match = true;

            $product->setGroupIdentity($identity);
        }
    }
}