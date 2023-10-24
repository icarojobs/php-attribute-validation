<?php

namespace App\Validations\Rules\Contracts;

interface ValidationRuleInterface
{
    public function getValidator(): ValidatorInterface|LengthValidatorInterface;
}