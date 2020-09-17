<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutLastSomeWordFromProductName
{
    private $cutWord;

    /**
     * CutLastSomeWordFromProductName constructor.
     * @param $cutLastWord
     */
    public function __construct($cutLastWord)
    {
        $this->cutWord = $cutLastWord;
    }

    public function __invoke(Product $product)
    {
        $name = $product->getName();
        if (strlen($name)) {
            $name = preg_split('/[\s+\\\\,.\/]+/', $name);
            if (count($name) > ($this->cutWord - 1)) {
                $array_slice = array_slice($name, 0, (count($name) - $this->cutWord));
                $product->setGroupIdentity(mb_strtolower(implode('_', $array_slice)));
            }
        }
    }
}