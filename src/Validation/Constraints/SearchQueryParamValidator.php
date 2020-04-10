<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SearchQueryParamValidator extends ConstraintValidator
{
    const pattern = '^[A-Za-z0-9 ,-éäöåÉÄÖÅ]*$';

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SearchQueryParam) {
            throw new UnexpectedTypeException($constraint, SearchQueryParam::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!preg_match('/' . self::pattern . '/', $value, $matches)
            || '' === $value
        ) {
            $this->context
                ->buildViolation('key ' . $this->context->getPropertyPath()
                    . ' with value "' . $value . '" not valid. Pattern:' . self::pattern)
                ->addViolation();
        }
    }
}