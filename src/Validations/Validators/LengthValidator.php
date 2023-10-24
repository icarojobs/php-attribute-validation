<?php

declare(strict_types=1);

namespace App\Validations\Validators;

use App\Validations\Rules\Contracts\LengthValidatorInterface;

class LengthValidator implements LengthValidatorInterface
{
    public function validate(mixed $value, int $min, int $max): bool
    {
        return mb_strlen($value) >= $min && mb_strlen($value) <= $max;
    }
}