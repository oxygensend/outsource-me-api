<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Url $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!preg_match("/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/"
            , $value)) {
            // TODO: implement the validation here
            $this->context->buildViolation($constraint->message)
                ->addViolation();

        }
    }
}
