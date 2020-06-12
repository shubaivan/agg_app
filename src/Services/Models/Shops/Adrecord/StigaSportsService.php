<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class StigaSportsService implements IdentityGroup
{
    /**
     * @var bool
     */
    private $match = false;

    public function identityGroupColumn(Product $product)
    {
        $this->hyphenRule($product);
        $this->dotRule($product);
        if (!$product->getGroupIdentity()) {
            $product->setGroupIdentity($product->getSku());
        }
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