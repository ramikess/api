<?php

declare(strict_types=1);

namespace App\Validator\Loan;

use App\Dto\Input\LoanCreateInput;
use App\Entity\Book;
use App\Entity\User;

interface LoanValidatorInterface
{
    public function validate(User $user, Book $book, LoanCreateInput $input): void;
}
