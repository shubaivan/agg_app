<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutLastSomeDigitFromEanAndFullName extends CutLastSomeDigitFromEan
{
    /**
     * CutLastSomeDigitFromEanAndFullName constructor.
     * @param int $cut
     */
    public function __construct(int $cut)
    {
        parent::__construct($cut);
    }

    public function __invoke(Product $product)
    {
        parent::__invoke($product);
        $name = $product->getName();
        if (strlen($name)) {
            $mb_strtolower = mb_strtolower(preg_replace('/[\s+,.]+/', '', $name));
            $product->setGroupIdentity($product->getGroupIdentity() .
                '_' . $mb_strtolower);
        }
    }
}