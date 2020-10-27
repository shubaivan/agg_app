<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class CutSomeBlocksByDelimiterFromSku extends AbstractStrategy
{
    public static $description = 'cut some block, divided by some delimiter, from sku';
    public static $requiredInputs = ['sku'];
    public static $requiredArgs = ['numberOfBlocks', 'delimiter', 'offsetSku'];

    protected $numberOfBlocks;

    protected $delimiter;

    protected $offsetSku = 0;

    /**
     * CutSomeBlocksByDelimiterFromSku constructor.
     * @param $numberOfBlocks
     * @param $delimiter
     * @param int|null $offsetSku
     */
    public function __construct($numberOfBlocks, $delimiter, ?int $offsetSku = 0)
    {
        $this->numberOfBlocks = $numberOfBlocks;
        $this->delimiter = $delimiter;
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
        $this->validateRequiredInputs($requiredInputs);
        /**
         * @var $sku
         */
        extract($requiredInputs);
        $identity = false;
        $explodeSku = explode($this->delimiter, $sku);
        if (count($explodeSku) >= $this->numberOfBlocks) {
            $blocks = array_slice($explodeSku, $this->offsetSku, $this->numberOfBlocks);
            $identity = implode($this->delimiter, $blocks);
        }

        return $identity;
    }
}