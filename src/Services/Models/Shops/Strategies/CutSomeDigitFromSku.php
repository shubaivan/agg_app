<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class CutSomeDigitFromSku extends AbstractStrategy
{
    public static $description = 'cut some symbol from sku';
    public static $requiredInputs = ['sku'];

    protected $cutFromSku;

    /**
     * CutSomeDigitFromSku constructor.
     * @param $cutFromSku
     */
    public function __construct($cutFromSku)
    {
        $this->cutFromSku = $cutFromSku;
    }

    /**
     * @param Product $product
     * @throws \Exception
     */
    public function __invoke(Product $product)
    {
        $coreAnalysis = $this->coreAnalysis(['sku' => $product->getSku()]);
        if ($coreAnalysis) {
            $product->setGroupIdentity($coreAnalysis);
        }
    }

    /**
     * @param array $requiredInputs
     * @return bool|string
     * @throws \Exception
     */
    function coreAnalysis(array $requiredInputs)
    {
        $this->validateRequiredInputs($requiredInputs);
        /**
         * @var $sku
         */
        extract($requiredInputs);
        $identity = false;

        if (strlen($sku)) {
            $identity = mb_substr($sku, 0, $this->cutFromSku);
        }

        return $identity;
    }
}