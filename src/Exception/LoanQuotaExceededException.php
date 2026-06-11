<?php

declare(strict_types=1);

namespace App\Exception;

final class LoanQuotaExceededException extends \DomainException
{
    public static function forUser(string $fullName, int $max): self
    {
        return new self(sprintf(
            'L\'utilisateur "%s" a atteint la limite de %d emprunts actifs.',
            $fullName,
            $max,
        ));
    }
}
