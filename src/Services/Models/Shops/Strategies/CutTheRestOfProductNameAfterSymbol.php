<?php


namespace App\Services\Models\Shops\Strategies;


use App\Entity\Product;

class CutTheRestOfProductNameAfterSymbol
{
    protected $delimiter;

    /**
     * CutTheRestOfProductNameAfterSymbol constructor.
     * @param $delimiter
     */
    public function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param Product $product
     */
    public function __invoke(Product $product)
    {
        $sku = $product->getName();
        $explodeName = explode($this->delimiter, $sku);
        if (count($explodeName)) {
            $identity = array_shift($explodeName);
            $product->setGroupIdentity($identity);
        }
    }
}