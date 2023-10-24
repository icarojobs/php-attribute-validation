<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\DTO\UserRegistration;
use App\Validations\Validator;

$userRegistration = new UserRegistration('Tio Jobs', 'admin@admin.com');

$validator = new Validator();
$validator->validate($userRegistration);
$errors = $validator->getErrors();

print_r($errors);