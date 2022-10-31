<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class DateTimeRange extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'The value of endDate cannot be bigger than startDate';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
