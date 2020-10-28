<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;

class CardooniaService extends AbstractShop
{
    /**
     * @var bool
     */
    private $match = false;

    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }
        $this->underscoreRule($product);
        $this->hyphenRule($product);
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