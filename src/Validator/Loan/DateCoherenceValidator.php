<?php

declare(strict_types=1);

namespace App\Validator\Loan;

use App\Dto\Input\LoanCreateInput;
use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.loan_validator')]
final readonly class DateCoherenceValidator implements LoanValidatorInterface
{
    public function validate(User $user, Book $book, LoanCreateInput $input): void
    {
        if ($input->startDate >= $input->endDate) {
            throw new \DomainException('La date de début doit être antérieure à la date de fin.');
        }
    }
}
