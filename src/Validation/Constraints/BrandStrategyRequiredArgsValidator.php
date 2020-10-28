<?php

namespace App\Validation\Constraints;

use App\Entity\BrandStrategy;
use App\Entity\Strategies;
use App\Repository\StrategiesRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BrandStrategyRequiredArgsValidator extends ConstraintValidator
{
    /**
     * @var StrategiesRepository
     */
    private $strategiesRepository;

    /**
     * BrandStrategyRequiredArgsValidator constructor.
     * @param StrategiesRepository $strategiesRepository
     */
    public function __construct(StrategiesRepository $strategiesRepository)
    {
        $this->strategiesRepository = $strategiesRepository;
    }

    /**
     * @param mixed $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        /** @var $entity BrandStrategy */
        if (!$entity instanceof BrandStrategy) {
            return;
        }

        $strategy = $entity->getStrategy();

        if (!$strategy instanceof Strategies) {
            $this->context->buildViolation('This relation {{ relation }} should not be blank')
                ->setParameter('{{ relation }}', 'strategy')
                ->atPath('strategy')
                ->addViolation();
        } else {
            $entityRequiredArgs = $entity->getRequiredArgs();
            $requiredArgs = $strategy->getRequiredArgs();
            foreach ($requiredArgs as $requiredArg) {
                if (!isset($entityRequiredArgs[$requiredArg])) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ args }}', $requiredArg)
                        ->atPath('requiredArgs')
                        ->addViolation();
                }
            }
        }
    }
}
