<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndFullName extends CutSomeDigitFromSku
{
    public static $description = 'cut some count of symbol from sku and plus full name';
    public static $requiredInputs = ['sku', 'name'];
    public static $requiredArgs = ['cutFromSku', 'offsetSku'];

    /**
     * CutSomeDigitFromSkuAndFullName constructor.
     * @param int $cutFromSku
     * @param int|null $offsetSku
     */
    public function __construct(int $cutFromSku, ?int $offsetSku = 0)
    {
        parent::__construct($cutFromSku, $offsetSku);
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
        $this->validateRequiredInputs($requiredInputs, self::$requiredInputs);
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