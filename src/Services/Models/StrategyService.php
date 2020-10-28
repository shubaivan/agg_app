<?php


namespace App\Services\Models;


use App\Entity\Strategies;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;
use Symfony\Component\HttpFoundation\ParameterBag;

class StrategyService
{

    /**
     * @param Strategies $strategy
     * @param ParameterBag $bagParam
     * @return mixed
     * @throws \ReflectionException
     */
    public function applyCoreAnalysis(
        Strategies $strategy,
        ParameterBag $bagParam
    )
    {
        $instance = $this->prepareStrategyInstanceWithArgs($strategy, $bagParam);

        $requiredInputs = $strategy->getRequiredInputs();
        $requiredInputsData = [];
        foreach ($requiredInputs as $input) {
            $requiredInputsData[$input] = $bagParam->get($input);
        }

        return $instance->coreAnalysis($requiredInputsData);
    }

    /**
     * @param Strategies $strategy
     * @param ParameterBag $bagParam
     * @return AbstractStrategy
     * @throws \ReflectionException
     */
    public function prepareStrategyInstanceWithArgs(Strategies $strategy, ParameterBag $bagParam): AbstractStrategy
    {
        $strategyNameSpace = $strategy->getStrategyNameSpace();
        $requiredArgs = $strategy->getRequiredArgs();
        $requiredArgsData = [];
        foreach ($requiredArgs as $arg) {
            $requiredArgsData[$arg] = $bagParam->get($arg);
        }
        $reflector = new \ReflectionClass($strategyNameSpace);
        /** @var AbstractStrategy $instance */
        $instance = $reflector->newInstanceArgs($requiredArgsData);
        return $instance;
    }
}