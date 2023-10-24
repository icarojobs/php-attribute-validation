<?php

namespace App\Validations\Rules\Contracts;

interface ValidatorInterface
{
    public function validate(mixed $value);
}