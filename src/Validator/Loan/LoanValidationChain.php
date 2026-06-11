<?php

declare(strict_types=1);

namespace App\Validator\Loan;

use App\Dto\Input\LoanCreateInput;
use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class LoanValidationChain
{
    /**
     * @param iterable<LoanValidatorInterface> $validators
     */
    public function __construct(
        #[AutowireIterator('app.loan_validator')]
        private iterable $validators,
    ) {}

    public function validate(User $user, Book $book, LoanCreateInput $input): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($user, $book, $input);
        }
    }
}
