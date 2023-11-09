<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsPasswordRequirements extends Constraint
{
    public $message = 'Le mot de passe doit comporter au moins 12 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.';
}