<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutFirstSomeWordFromProductName
{
    private $cutWord;

    /**
     * CutFirstSomeWordFromProductName constructor.
     * @param $cutWord
     */
    public function __construct($cutWord)
    {
        $this->cutWord = $cutWord;
    }

    public function __invoke(Product $product)
    {
        $preg_split = preg_split('/[\s+\\\\,.\/]+/', $product->getName(), ($this->cutWord + 1));
        if (count($preg_split) > ($this->cutWord - 1)) {
            $array_slice = array_slice($preg_split, 0, $this->cutWord);
            if (count($array_slice)) {
                $product->setGroupIdentity(mb_strtolower(implode('_', $array_slice)));
            }
        }
    }
}