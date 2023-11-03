<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OpenHoursValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Vous pouvez implémenter ici la logique de validation des heures d'ouverture
        // Par exemple, vérifiez si $value (heure de rendez-vous) est dans les heures d'ouverture autorisées.
        $currentHour = (int) $value->format('H');
        if ($currentHour < 8 || $currentHour >= 18) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
