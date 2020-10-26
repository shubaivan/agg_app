<?php


namespace App\Services\Models\Shops\Strategies\Common;


abstract class AbstractStrategy
{
    public static $description = '';
    public static $requiredInputs = [];

    public static function requireProperty()
    {
        return ['description', 'requiredInputs'];
    }

    abstract function coreAnalysis(array $requiredInputs);

    /**
     * @param array $data
     * @throws \Exception
     */
    protected function validateRequiredInputs(array $data) {
        $requiredInputs = static::$requiredInputs;
        foreach ($requiredInputs as $input) {
            if (!array_key_exists($input, $data)) {
                throw new \Exception('requiredInputs not valid');
            }
        }
    }
}