<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutLastSomeDigitFromSkuAndEanAndFirstWordFromName extends CutSomeDigitFromSkuAndSomeFromEan
{

    /**
     * CutLastSomeDigitFromSkuAndEanAndFirstWordFromName constructor.
     * @param $cutFromEan
     * @param $cutFromSku
     */
    public function __construct($cutFromEan, $cutFromSku)
    {
        parent::__construct($cutFromEan, $cutFromSku);
    }

    public function __invoke(Product $product)
    {
        parent::__invoke($product);
        $name = $product->getName();
        if (strlen($name)) {
            $preg_split = preg_split('/[\s,\/]+/', $name, 2);
            if (count($preg_split)) {
                $array_slice = array_slice($preg_split, 0, 1);
                if (count($array_slice)) {
                    $product->setGroupIdentity(
                        $product->getGroupIdentity() . '_' . mb_strtolower(implode('_', $array_slice))
                    );
                }
            }
        }
    }
}