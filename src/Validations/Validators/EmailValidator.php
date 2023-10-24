<?php

declare(strict_types=1);

namespace App\Validations\Validators;

use App\Validations\Rules\Contracts\ValidatorInterface;

class EmailValidator implements ValidatorInterface
{
    public function validate(mixed $value): bool
    {
        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}