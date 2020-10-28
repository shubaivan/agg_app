<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;

class StigaSportsService extends AbstractShop
{
    /**
     * @var bool
     */
    private $match = false;

    /**
     * @param Product $product
     * @return Product|bool|mixed|void
     * @throws \ReflectionException
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