<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;

class CutSomeDigitFromEanAndFullName extends CutSomeDigitFromEan
{
    public static $description = 'cut some symbol from ean and plus full name';
    public static $requiredInputs = ['ean', 'name'];

    /**
     * CutSomeDigitFromEanAndFullName constructor.
     * @param int $cut
     */
    public function __construct(int $cut)
    {
        parent::__construct($cut);
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