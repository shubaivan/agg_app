<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeWordFromProductName
{
    private $cutWord;

    private $pattern = '\s+\\\\,.\/';

    /**
     * CutSomeWordFromProductName constructor.
     * @param $cutWord
     * @param string|null $pattern
     */
    public function __construct($cutWord, ?string $pattern = null)
    {
        $this->cutWord = $cutWord;
        if ($pattern) {
            $this->pattern = $pattern;
        }
    }

    public function __invoke(Product $product)
    {
        $name = $product->getName();
        if (strlen($name)) {
            $name = preg_split('/['.$this->pattern.']+/', $name);
            if (count($name)) {
                $array_slice = array_slice(
                    $name,
                    0,
                    $this->cutWord
                );
                $product->setGroupIdentity(mb_strtolower(implode('_', $array_slice)));
            }
        }
    }
}