<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ExtraFieldsValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ExtraFields) {
            throw new UnexpectedTypeException($constraint, ExtraFields::class);
        }

        if (null === $value || '' === $value || '0' === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        foreach ($value as $item) {
            if (!preg_match('/^[A-Za-z0-9 ,-éäöåÉÄÖÅ]*$/', $item, $matches)
                || null === $item || '' === $item
            ) {
                $this->context
                    ->buildViolation('key ' . $this->context->getPropertyPath() . ' with value "' . $item . '" not valid')
                    ->addViolation();
            }
        }
    }
}