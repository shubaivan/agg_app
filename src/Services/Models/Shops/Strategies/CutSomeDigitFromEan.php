<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class CutSomeDigitFromEan extends AbstractStrategy
{
    public static $description = 'cut some symbol from ean';
    public static $requiredInputs = ['ean'];

    protected $cutFromEan;

    /**
     * CutSomeDigitFromEan constructor.
     * @param $cutFromEan
     */
    public function __construct($cutFromEan)
    {
        $this->cutFromEan = $cutFromEan;
    }

    /**
     * @param Product $product
     * @throws \Exception
     */
    public function __invoke(Product $product)
    {
        $coreAnalysis = $this->coreAnalysis(['ean' => $product->getEan()]);
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
         * @var $ean
         */
        extract($requiredInputs);
        $identity = false;

        if (strlen($ean)) {
            $identity = mb_substr($ean, 0, $this->cutFromEan);
        }

        return $identity;
    }
}