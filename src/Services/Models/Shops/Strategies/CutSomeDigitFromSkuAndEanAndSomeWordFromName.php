<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromSkuAndEanAndSomeWordFromName extends CutSomeDigitFromSkuAndSomeFromEan
{
    public static $description = 'cut some count of symbol from sku and ean and plus cut some count of word from name';
    public static $requiredInputs = ['sku', 'ean', 'name'];
    public static $requiredArgs = [
        'cutFromEan', 'cutFromSku', 'cutWord',
        'offsetEan', 'offsetSku', 'offsetWord'
    ];

    private $cutWord;

    protected $offsetWord = 0;

    /**
     * CutSomeDigitFromSkuAndEanAndSomeWordFromName constructor.
     * @param $cutFromEan
     * @param $cutFromSku
     * @param $cutWord
     * @param int|null $offsetEan
     * @param int|null $offsetSku
     * @param int|null $offsetWord
     */
    public function __construct(
        $cutFromEan, $cutFromSku, $cutWord,
        ?int $offsetEan = 0, ?int $offsetSku = 0, ?int $offsetWord = 0
    )
    {
        parent::__construct($cutFromEan, $cutFromSku, $offsetSku, $offsetEan);
        $this->cutWord = $cutWord;
        if ($offsetWord) {
            $this->offsetWord = $offsetWord;
        }
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
            $preg_split = preg_split('/[\s\-+\\\\,.\/]+/', $name);
            if (count($preg_split) > ($this->cutWord - 1)) {
                $array_slice = array_slice($preg_split, $this->offsetWord, $this->cutWord);
                if (count($array_slice)) {
                    $identity .= '_' . mb_strtolower(implode('_', $array_slice));
                }
            }
        }

        return $identity;
    }
}