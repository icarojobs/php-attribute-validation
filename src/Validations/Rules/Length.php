<?php

declare(strict_types=1);

namespace App\Validations\Rules;

use App\Validations\Rules\Contracts\ValidationRuleInterface;
use App\Validations\Rules\Contracts\LengthValidatorInterface;
use App\Validations\Validators\LengthValidator;

#[\Attribute]
class Length implements ValidationRuleInterface
{
    public function __construct(
        public int $min,
        public int $max,
		) { }

    public function getValidator(): LengthValidatorInterface
    {
        return new LengthValidator();
    }
}