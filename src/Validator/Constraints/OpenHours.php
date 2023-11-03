<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OpenHours extends Constraint
{
    public $message = 'Les réservations ne sont pas autorisées en dehors des heures d\'ouverture.';
}