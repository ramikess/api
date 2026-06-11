<?php

declare(strict_types=1);

namespace App\Exception;

final class BookNotAvailableException extends \DomainException
{
    public static function alreadyBorrowed(string $title): self
    {
        return new self(sprintf('Le livre "%s" est déjà emprunté.', $title));
    }
}
