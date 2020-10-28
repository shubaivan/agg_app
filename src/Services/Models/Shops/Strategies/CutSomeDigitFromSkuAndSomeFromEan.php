<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndSomeFromEan extends CutSomeDigitFromEan
{
    public static $description = 'cut some count of symbol from sku and ean';
    public static $requiredInputs = ['sku', 'ean'];
    public static $requiredArgs = [
        'cutFromEan', 'cutFromSku',
        'offsetEan', 'offsetSku',
    ];

    protected $cutFromSku;

    protected $offsetSku = 0;

    /**
     * @param array $requiredArgs
     */
    public static function setRequiredArgs(array $requiredArgs): void
    {
        self::$requiredArgs = $requiredArgs;
    }

    /**
     * CutSomeDigitFromSkuAndSomeFromEan constructor.
     * @param $cutFromEan
     * @param $cutFromSku
     * @param int|null $offsetSku
     * @param int|null $offsetEan
     */
    public function __construct(
        $cutFromEan, $cutFromSku,
        ?int $offsetSku = 0, ?int $offsetEan = 0
    )
    {
        parent::__construct($cutFromEan, $offsetEan);
        $this->cutFromSku = $cutFromSku;
        if ($offsetSku) {
            $this->offsetSku = $offsetSku;
        }
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
        $this->validateRequiredInputs($requiredInputs, self::$requiredInputs);
        /**
         * @var $ean
         * @var $sku
         */
        extract($requiredInputs);
        $identity = parent::coreAnalysis([
            'ean' => $ean
        ]);

        if (strlen($sku)) {
            $identity .= '_' . mb_substr($sku, $this->offsetSku, $this->cutFromSku);
        }

        return $identity;
    }
}