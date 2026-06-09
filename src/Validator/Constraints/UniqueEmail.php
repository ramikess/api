<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'Cet email "{{ email }}" est déjà utilisé.';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
