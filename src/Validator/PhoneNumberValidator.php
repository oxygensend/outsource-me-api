<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\PhoneNumber $constraint */

        if (null === $value || '' === $value) {
            return;
        }


        if (!preg_match('/[1-9]([0-9]){8}/', $value)) {

            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

        }
    }
}
