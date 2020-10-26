<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class CutSomeWordsFromProductNameByDelimiter extends AbstractStrategy
{
    public static $description = 'cut some word, divided by delimiter, from name';
    public static $requiredInputs = ['name'];

    private $cutWord;

    private $pattern = '\s+\\\\,.\/';

    /**
     * CutSomeWordsFromProductNameByDelimiter constructor.
     * @param $cutWord
     * @param string|null $pattern
     */
    public function __construct($cutWord, ?string $pattern = null)
    {
        $this->cutWord = $cutWord;
        if ($pattern) {
            $this->pattern = $pattern;
        }
    }

    /**
     * @param Product $product
     * @throws \Exception
     */
    public function __invoke(Product $product)
    {
        $coreAnalysis = $this->coreAnalysis([
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
         * @var $name
         */
        extract($requiredInputs);
        $identity = false;

        if (strlen($name)) {
            $name = preg_split('/['.$this->pattern.']+/', $name);
            if (count($name)) {
                $array_slice = array_slice(
                    $name,
                    0,
                    $this->cutWord
                );
                $identity = mb_strtolower(implode('_', $array_slice));
            }
        }

        return $identity;
    }
}