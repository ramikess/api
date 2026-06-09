<?php

declare(strict_types=1);

namespace App\Enum;

enum LoanStatus: string
{
    case Active = 'active';
    case Returned = 'returned';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::Active => 'En cours',
            self::Returned => 'Rendu',
            self::Overdue => 'En retard',
            self::Cancelled => 'Annulé',
        };
    }
}
