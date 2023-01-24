<?php

namespace App\Validator;

use App\Entity\Education;
use App\Entity\JobPosition;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateTimeRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\DateTimeRange $constraint */
        /* @var JobPosition|Education $value */

        if (null === $value) {
            return;
        }

        if ($value->getStartDate() >= $value->getEndDate() && $value->getEndDate()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
