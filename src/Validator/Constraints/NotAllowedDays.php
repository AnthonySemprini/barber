<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NotAllowedDays extends Constraint
{
    public $message = 'Le salon est férmé  les lundis et les dimanches.';
}
