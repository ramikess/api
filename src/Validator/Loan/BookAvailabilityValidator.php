<?php

declare(strict_types=1);

namespace App\Validator\Loan;

use App\Dto\Input\LoanCreateInput;
use App\Entity\Book;
use App\Entity\User;
use App\Exception\BookNotAvailableException;
use App\Repository\LoanRepository;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.loan_validator')]
final readonly class BookAvailabilityValidator implements LoanValidatorInterface
{
    public function __construct(
        private LoanRepository $loanRepository,
    ) {}

    public function validate(User $user, Book $book, LoanCreateInput $input): void
    {
        if ($this->loanRepository->hasOverlappingLoan($book, $input->startDate, $input->endDate)) {
            throw BookNotAvailableException::alreadyBorrowed($book->getTitle());
        }
    }
}
