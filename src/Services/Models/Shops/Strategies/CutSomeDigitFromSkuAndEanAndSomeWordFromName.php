<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndEanAndSomeWordFromName extends CutSomeDigitFromSkuAndSomeFromEan
{
    public static $description = 'cut some count of symbol from sku and ean and plus cut some count of word from name';
    public static $requiredInputs = ['sku', 'ean', 'name'];

    private $cutWord;

    /**
     * CutSomeDigitFromSkuAndEanAndSomeWordFromName constructor.
     * @param $cutFromEan
     * @param $cutFromSku
     * @param $cutWord
     */
    public function __construct($cutFromEan, $cutFromSku, $cutWord)
    {
        parent::__construct($cutFromEan, $cutFromSku);
        $this->cutWord = $cutWord;
    }

    public function __invoke(Product $product)
    {
        $coreAnalysis = $this->coreAnalysis([
            'sku' => $product->getSku(),
            'ean' => $product->getEan(),
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
         * @var $ean
         * @var $sku
         * @var $name
         */
        extract($requiredInputs);
        $identity = parent::coreAnalysis([
            'sku' => $sku,
            'ean' => $ean
        ]);

        if ($name) {
            $preg_split = preg_split('/[\s+\\\\,.\/]+/', $name, ($this->cutWord + 1));
            if (count($preg_split) > ($this->cutWord - 1)) {
                $array_slice = array_slice($preg_split, 0, $this->cutWord);
                if (count($array_slice)) {
                    $identity .= '_' . mb_strtolower(implode('_', $array_slice));
                }
            }
        }

        return $identity;
    }
}