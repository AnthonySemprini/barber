<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateNotPassedValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $currentDate = new \DateTime();
        
        if ($value < $currentDate) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}


