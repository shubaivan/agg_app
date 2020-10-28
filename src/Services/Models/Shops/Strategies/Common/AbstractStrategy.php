<?php


namespace App\Services\Models\Shops\Strategies\Common;


use App\Entity\Product;

abstract class AbstractStrategy
{
    public static $description = '';
    public static $requiredInputs = [];
    public static $requiredArgs = [];

    public static function requireProperty()
    {
        return ['description', 'requiredInputs', 'requiredArgs'];
    }

    abstract function coreAnalysis(array $requiredInputs);

    abstract function __invoke(Product $product);

    /**
     * @param array $data
     * @param array $ri
     * @throws \Exception
     */
    protected function validateRequiredInputs(array $data, array $ri = []) {
        if ($ri) {
            $requiredInputs = $ri;
        } else {
            $requiredInputs = static::$requiredInputs;
        }

        foreach ($requiredInputs as $input) {
            if (!array_key_exists($input, $data)) {
                throw new \Exception('requiredInputs not valid');
            }
        }
    }
}