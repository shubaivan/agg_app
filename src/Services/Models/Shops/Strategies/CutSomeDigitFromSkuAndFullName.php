<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndFullName extends CutSomeDigitFromSku
{
    /**
     * CutSomeDigitFromSkuAndFullName constructor.
     * @param int $cutFromSku
     */
    public function __construct(int $cutFromSku)
    {
        parent::__construct($cutFromSku);
    }

    public function __invoke(Product $product)
    {
        parent::__invoke($product);
        $name = $product->getName();
        if (strlen($name)) {
            $mb_strtolower = mb_strtolower(preg_replace('/[\s+,.]+/', '', $name));
            $product->setGroupIdentity($product->getGroupIdentity() . '_' . $mb_strtolower);
        }
    }
}