<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class CutSomeDigitFromEan extends AbstractStrategy
{
    public static $description = 'cut some symbol from ean';
    public static $requiredInputs = ['ean'];
    public static $requiredArgs = ['cutFromEan', 'offsetEan'];

    protected $cutFromEan;

    protected $offsetEan = 0;

    /**
     * CutSomeDigitFromEan constructor.
     * @param $cutFromEan
     * @param int|null $offsetEan
     */
    public function __construct($cutFromEan, ?int $offsetEan = 0)
    {
        $this->cutFromEan = $cutFromEan;
        if ($offsetEan) {
            $this->offsetEan = $offsetEan;
        }
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
        $this->validateRequiredInputs($requiredInputs, self::$requiredInputs);
        /**
         * @var $ean
         */
        extract($requiredInputs);
        $identity = false;

        if (strlen($ean)) {
            $identity = mb_substr($ean, $this->offsetEan, $this->cutFromEan);
        }

        return $identity;
    }
}