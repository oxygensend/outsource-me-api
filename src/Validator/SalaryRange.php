<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
* @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class SalaryRange extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $messageRangesAreEqual = 'Ranges cannot be equal, if you dont want to set range, please leave `upRange` property empty';
    public string $messageUpperRangeIsLower =  'The value of upRange must by greater than downRange';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
