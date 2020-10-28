<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class CutSomeWordsFromProductNameByDelimiter extends AbstractStrategy
{
    public static $description = 'cut some word, divided by delimiter, from name';
    public static $requiredInputs = ['name'];
    public static $requiredArgs = ['cutWord', 'pattern', 'offsetName'];

    private $cutWord;

    private $pattern = '\s+\\\\,.\/';

    protected $offsetName = 0;

    /**
     * CutSomeWordsFromProductNameByDelimiter constructor.
     * @param $cutWord
     * @param string|null $pattern
     * @param int|null $offsetName
     */
    public function __construct($cutWord, ?string $pattern = null, ?int $offsetName = 0)
    {
        $this->cutWord = $cutWord;
        if ($pattern) {
            $this->pattern = $pattern;
        }
        if ($offsetName) {
            $this->offsetName = $offsetName;
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
                    $this->offsetName,
                    $this->cutWord
                );
                $identity = mb_strtolower(implode('_', $array_slice));
            }
        }

        return $identity;
    }
}