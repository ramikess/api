<?php

declare(strict_types=1);

namespace App\Exception;

final class EntityNotFoundException extends \RuntimeException
{
    public static function for(string $class, int $id): self
    {
        return new self(sprintf('%s #%d introuvable.', $class, $id));
    }
}
