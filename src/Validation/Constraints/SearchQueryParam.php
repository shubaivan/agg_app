<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SearchQueryParam extends Constraint
{
    public $message = '';
}