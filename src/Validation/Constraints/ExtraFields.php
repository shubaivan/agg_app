<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExtraFields extends Constraint
{
    public $message = '';
}