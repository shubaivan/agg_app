<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndFullName extends CutSomeDigitFromSku
{
    public static $description = 'cut some count of symbol from sku and plus full name';
    public static $requiredInputs = ['sku', 'name'];

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
        $coreAnalysis = $this->coreAnalysis([
            'sku' => $product->getSku(),
            'name' => $product->getName()
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
         * @var $sku
         * @var $name
         */
        extract($requiredInputs);
        $identity = parent::coreAnalysis([
            'sku' => $sku
        ]);

        if (strlen($name)) {
            $identity .= '_' . mb_strtolower(preg_replace('/[\s+,.]+/', '', $name));
        }

        return $identity;
    }
}