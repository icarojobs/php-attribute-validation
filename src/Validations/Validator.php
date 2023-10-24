<?php

declare(strict_types=1);

namespace App\Validations;

use App\Validations\Rules\Contracts\ValidationRuleInterface;

class Validator
{
    private array $errors = [];

    public function validate(object $object): void
    {
        $reflector = new \ReflectionClass($object);

        foreach ($reflector->getProperties() as $property) {
            $attributes = $property->getAttributes(
                name: ValidationRuleInterface::class,
                flags: \ReflectionAttribute::IS_INSTANCEOF,
            );

            foreach ($attributes as $attribute) {
                $validator = $attribute->newInstance()->getValidator();

                if (!$validator->validate($property->getValue($object), ...$attribute->getArguments())) {
                    $this->errors[$property->getName()][] = sprintf(
                        "Invalid value for '%s' using '%s' validation.",
                        $property->getName(),
                        $attribute->getName()   
                    );
                }
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}