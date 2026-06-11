<?php

declare(strict_types=1);

namespace App\Validator\Loan;

use App\Dto\Input\LoanCreateInput;
use App\Entity\Book;
use App\Entity\User;
use App\Exception\LoanQuotaExceededException;
use App\Repository\LoanRepository;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.loan_validator')]
final readonly class UserQuotaValidator implements LoanValidatorInterface
{
    public function __construct(
        private LoanRepository $loanRepository,
        private int $maxActiveLoans = 3,
    ) {}

    public function validate(User $user, Book $book, LoanCreateInput $input): void
    {
        if ($this->loanRepository->countActiveLoansForUser($user) >= $this->maxActiveLoans) {
            throw new LoanQuotaExceededException(sprintf(
                'L\'utilisateur "%s" a atteint la limite de %d emprunts actifs.',
                $user->getEmail(),
                $this->maxActiveLoans,
            ));
        }
    }
}
