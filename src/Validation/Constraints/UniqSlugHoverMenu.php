<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * @Annotation
 */
class UniqSlugHoverMenu extends Constraint
{
    public $message = 'This value {{ uniq_value }} should be uniq, but exist in id: {{ exist_category_id }}.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
