<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class CutSomeDigitFromSku extends AbstractStrategy
{
    public static $description = 'cut some symbol from sku';
    public static $requiredInputs = ['sku'];
    public static $requiredArgs = ['cutFromSku', 'offsetSku'];

    protected $cutFromSku;

    protected $offsetSku = 0;

    /**
     * CutSomeDigitFromSku constructor.
     * @param $cutFromSku
     * @param int|null $offsetSku
     */
    public function __construct($cutFromSku, ?int $offsetSku = 0)
    {
        $this->cutFromSku = $cutFromSku;
        if ($offsetSku) {
            $this->offsetSku = $offsetSku;
        }
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
        $this->validateRequiredInputs($requiredInputs, self::$requiredInputs);
        /**
         * @var $sku
         */
        extract($requiredInputs);
        $identity = false;

        if (strlen($sku)) {
            $identity = mb_substr($sku, $this->offsetSku, $this->cutFromSku);
        }

        return $identity;
    }
}