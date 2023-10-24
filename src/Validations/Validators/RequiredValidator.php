<?php

declare(strict_types=1);

namespace App\Validations\Validators;

use App\Validations\Rules\Contracts\ValidatorInterface;

class RequiredValidator implements ValidatorInterface
{
    public function validate(mixed $value): bool
    {
        return !empty($value);
    }
}