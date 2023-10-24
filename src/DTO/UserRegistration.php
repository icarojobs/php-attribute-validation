<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validations\Rules\Required;
use App\Validations\Rules\Email;
use App\Validations\Rules\Length;

readonly final class UserRegistration
{
    public function __construct(
        #[Required]
        #[Length(min: 10, max: 255)]
        public string $user,

        #[Required]
        #[Email]
        public string $email,
    ) { }
}