<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSku
{

    protected $cutFromSku;

    /**
     * CutSomeDigitFromSku constructor.
     * @param $cutFromSku
     */
    public function __construct($cutFromSku)
    {
        $this->cutFromSku = $cutFromSku;
    }


    public function __invoke(Product $product)
    {
        $sku = $product->getSku();
        if (strlen($sku)) {
            $product->setGroupIdentity(mb_substr($sku, 0, $this->cutFromSku));
        }
    }
}