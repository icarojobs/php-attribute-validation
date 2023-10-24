<?php

namespace App\Validations\Rules\Contracts;

interface LengthValidatorInterface
{
    public function validate(mixed $value, int $min, int $max);
}