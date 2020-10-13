<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeBlocksByDelimiterFromSku
{
    protected $numberOfBlocks;

    protected $delimiter;

    /**
     * CutSomeBlocksByDelimiterFromSku constructor.
     * @param $numberOfBlocks
     * @param $delimiter
     */
    public function __construct($numberOfBlocks, $delimiter)
    {
        $this->numberOfBlocks = $numberOfBlocks;
        $this->delimiter = $delimiter;
    }

    /**
     * @param Product $product
     */
    public function __invoke(Product $product)
    {
        $sku = $product->getSku();
        $explodeSku = explode($this->delimiter, $sku);
        if (count($explodeSku) >= ($this->numberOfBlocks + 1)) {
            $blocks = array_slice($explodeSku, 0, $this->numberOfBlocks);
            $identity = implode($this->delimiter, $blocks);
            $product->setGroupIdentity($identity);
        }
    }
}