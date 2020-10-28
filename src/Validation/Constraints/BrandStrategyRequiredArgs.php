<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * @Annotation
 */
class BrandStrategyRequiredArgs extends Constraint
{
    public $message = 'This value {{ args }} should be present';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
