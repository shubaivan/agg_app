<?php


namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class FullEan extends AbstractStrategy
{
    public static $description = 'full ean';
    public static $requiredInputs = ['ean'];

    /**
     * @param Product $product
     * @throws \Exception
     */
    public function __invoke(Product $product)
    {
        $coreAnalysis = $this->coreAnalysis([
            'ean' => $product->getEan()
        ]);
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
            $identity = $ean;
        }

        return $identity;
    }
}