<?php 

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotAllowedDaysValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Vous pouvez implémenter ici la logique de validation pour les jours interdits
        $dayOfWeek = (int) $value->format('N'); // 1 (lundi) à 7 (dimanche)
        if ($dayOfWeek === 1 || $dayOfWeek === 7) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
