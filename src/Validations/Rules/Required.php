<?php

declare(strict_types=1);

namespace App\Validations\Rules;

use App\Validations\Rules\Contracts\ValidationRuleInterface;
use App\Validations\Rules\Contracts\ValidatorInterface;
use App\Validations\Validators\RequiredValidator;

#[\Attribute]
class Required implements ValidationRuleInterface
{
    public function getValidator(): ValidatorInterface
    {
        return new RequiredValidator();
    }
}