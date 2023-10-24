<?php

declare(strict_types=1);

namespace App\Validations\Rules;

use App\Validations\Rules\Contracts\ValidationRuleInterface;
use App\Validations\Rules\Contracts\ValidatorInterface;
use App\Validations\Validators\EmailValidator;

#[\Attribute]
class Email implements ValidationRuleInterface
{
    public function getValidator(): ValidatorInterface
    {
        return new EmailValidator();
    }
}