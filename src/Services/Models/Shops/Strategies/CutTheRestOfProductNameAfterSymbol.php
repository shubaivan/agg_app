<?php


namespace App\Services\Models\Shops\Strategies;


use App\Entity\Product;

class CutTheRestOfProductNameAfterSymbol
{
    protected $numberOfBlocks;

    protected $delimiter;

    /**
     * CutTheRestOfProductNameAfterSymbol constructor.
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
        $name = $product->getName();
        $explodeName = explode($this->delimiter, $name);
        if (count($explodeName) >= $this->numberOfBlocks) {
            $blocks = array_slice($explodeName, 0, $this->numberOfBlocks);
            $identity = implode($this->delimiter, $blocks);
            $product->setGroupIdentity($identity);
        }
    }
}