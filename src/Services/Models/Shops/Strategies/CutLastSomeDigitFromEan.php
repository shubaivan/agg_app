<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutLastSomeDigitFromEan
{

    protected $cutFromEan;

    /**
     * CutLastSomeDigitFromEan constructor.
     * @param $cutFromEan
     */
    public function __construct($cutFromEan)
    {
        $this->cutFromEan = $cutFromEan;
    }

    public function __invoke(Product $product)
    {
        $ean = $product->getEan();
        if (strlen($ean)) {
            $product->setGroupIdentity(mb_substr($ean, 0, $this->cutFromEan));
        }
    }
}