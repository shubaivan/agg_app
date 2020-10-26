<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndSomeFromEan extends CutSomeDigitFromEan
{
    public static $description = 'cut some count of symbol from sku and ean';
    public static $requiredInputs = ['sku', 'ean'];

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
        $coreAnalysis = $this->coreAnalysis([
            'ean' => $product->getEan(),
            'sku' => $product->getSku()
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
         * @var $sku
         */
        extract($requiredInputs);
        $identity = parent::coreAnalysis([
            'ean' => $ean
        ]);

        if (strlen($sku)) {
            $identity .= '_' . mb_substr($sku, 0, $this->cutFromSku);
        }

        return $identity;
    }
}