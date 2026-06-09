<?php

declare(strict_types=1);

namespace App\Mapper;

use App\ApiResource\LoanResource;
use App\Entity\Loan;

class LoanResourceMapper
{
    public function map(Loan $loan): LoanResource
    {
        $resource = new LoanResource();

        $resource->id          = $loan->getId();
        $resource->startDate   = $loan->getStartDate();
        $resource->endDate     = $loan->getEndDate();
        $resource->returnedAt  = $loan->getReturnedAt();
        $resource->status      = $loan->getStatus()->value;
        $resource->statusLabel = $loan->getStatus()->getLabel();
        $resource->isOverdue   = $loan->isOverdue();

        $user = $loan->getUser();
        $resource->user = [
            'id'        => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName'  => $user->getLastName(),
            'email'     => $user->getEmail(),
        ];

        $book = $loan->getBook();
        $resource->book = [
            'id'          => $book->getId(),
            'title'       => $book->getTitle(),
            'description' => $book->getDescription(),
        ];

        $diff = (new \DateTimeImmutable())->diff($loan->getEndDate());

        if ($resource->isOverdue) {
            $resource->daysLate      = $diff->days;
            $resource->daysRemaining = null;
        } else {
            $resource->daysRemaining = $diff->days;
            $resource->daysLate      = null;
        }

        return $resource;
    }
}
