<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


class DateNotPassed extends Constraint
{
    public $message = 'La date du rendez-vous ne peut pas être passée.';
}