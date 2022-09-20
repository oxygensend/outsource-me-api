<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsPasswordConfirmedValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var IsPasswordConfirmed $constraint */
        /* @var User $value */

        if (null === $value)
            return;

        if (!$value->getPlainPassword() || !$value->getPasswordConfirmation())
            return;

        if ($value->getPlainPassword() !== $value->getPasswordConfirmation()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
            {
            }
        }
    }
}
