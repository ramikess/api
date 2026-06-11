<?php

namespace App\Handler;

use App\Dto\Input\LoanCreateInput;
use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\User;

class CreateLoanHandler
{

    public function createLoan(User $user, Book $book, LoanCreateInput $data): Loan
    {
        $loan = new Loan();
        $loan->setUser($user);
        $loan->setBook($book);
        $loan->setStartDate($data->startDate);
        $loan->setEndDate($data->endDate);

        return $loan;
    }
}
