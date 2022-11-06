<?php

namespace App\Validator;

use App\Entity\JobOffer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SalaryRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\SalaryRange $constraint */
        /** @var \App\Entity\SalaryRange $value */

        if (null === $value || $value->getDownRange() === null) {
            return;
        }

        if($value->getUpRange() && $value->getDownRange() === null){
            return;
        }

        if ($value->getUpRange() === $value->getDownRange()) {
            $this->context->buildViolation($constraint->messageRangesAreEqual)
                ->addViolation();
        }

        if ($value->getUpRange() < $value->getDownRange()) {
            $this->context->buildViolation($constraint->messageUpperRangeIsLower)
                ->addViolation();
        }

    }
}
