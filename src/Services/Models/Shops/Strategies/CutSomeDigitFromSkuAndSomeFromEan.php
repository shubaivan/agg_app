<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndSomeFromEan extends CutSomeDigitFromEan
{
    protected $cutFromSku;

    /**
     * CutSomeDigitFromSkuAndSomeFromEan constructor.
     * @param $cutFromEan
     * @param $cutFromSku
     */
    public function __construct($cutFromEan, $cutFromSku)
    {
        parent::__construct($cutFromEan);
        $this->cutFromSku = $cutFromSku;
    }


    public function __invoke(Product $product)
    {
        parent::__invoke($product);
        $sku = $product->getSku();
        if (strlen($sku)) {
            $product->setGroupIdentity(
                $product->getGroupIdentity() .
                '_' . mb_substr($sku, 0, $this->cutFromSku)
            );
        }
    }
}