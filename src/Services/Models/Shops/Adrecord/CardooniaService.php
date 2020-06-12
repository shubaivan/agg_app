<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;

class CardooniaService implements IdentityGroup
{
    /**
     * @var bool
     */
    private $match = false;

    public function identityGroupColumn(Product $product)
    {
        $this->underscoreRule($product);
        $this->hyphenRule($product);
        if (!$product->getGroupIdentity()) {
            $product->setGroupIdentity($product->getSku());
        }
    }

    public function underscoreRule(Product $product)
    {
        if ($this->match) {
            return;
        }
        $sku = $product->getSku();
        $explodeSku = explode('_', $sku);
        if (count($explodeSku) === 2) {
            array_pop($explodeSku);
            $identity = implode('', $explodeSku);
            $product->setGroupIdentity($identity);
            $this->match = true;
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
            $twoElemntFromBegin = array_slice($explodeSku, 0, 2);
            $identity = implode('-', $twoElemntFromBegin);
            $product->setGroupIdentity($identity);
            $this->match = true;
        }
    }
}