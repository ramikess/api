<?php

declare(strict_types=1);

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

class LoanCreateInput
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $bookId;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Assert\GreaterThanOrEqual('today', message: 'La date de début ne peut pas être dans le passé')]
    public \DateTimeImmutable $startDate;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Assert\GreaterThan(propertyPath: 'startDate', message: 'La date de fin doit être après la date de début')]
    public \DateTimeImmutable $endDate;
}
