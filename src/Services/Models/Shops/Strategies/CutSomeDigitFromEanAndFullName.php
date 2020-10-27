<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromEanAndFullName extends CutSomeDigitFromEan
{
    public static $description = 'cut some symbol from ean and plus full name';
    public static $requiredInputs = ['ean', 'name'];
    public static $requiredArgs = ['cutFromEan', 'offsetEan'];

    /**
     * CutSomeDigitFromEanAndFullName constructor.
     * @param int $cutFromEan
     * @param int|null $offsetEan
     */
    public function __construct(int $cutFromEan, ?int $offsetEan = 0)
    {
        parent::__construct($cutFromEan, $offsetEan);
    }

    public function __invoke(Product $product)
    {
        $coreAnalysis = $this->coreAnalysis([
            'name' => $product->getName(),
            'ean' => $product->getEan()
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
         * @var $ean
         */
        extract($requiredInputs);
        $identity = parent::coreAnalysis(['ean' => $ean]);

        if (strlen($name)) {
            $identity .= '_' . mb_strtolower(
                    preg_replace('/[\s+,.]+/', '', $name)
                );
        }

        return $identity;
    }
}