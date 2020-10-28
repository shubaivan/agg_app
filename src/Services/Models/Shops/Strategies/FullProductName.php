<?php

namespace App\Services\Models\Shops\Strategies;

use App\Entity\Product;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;

class FullProductName extends AbstractStrategy
{
    public static $description = 'full product name';
    public static $requiredInputs = ['name'];

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
            $identity = preg_replace('/[\s+,.]+/', '_', $name);
        }

        return $identity;
    }
}