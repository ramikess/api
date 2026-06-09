<?php

declare(strict_types=1);

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

class LoanReturnInput
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $loanId;

    #[Assert\Type(\DateTimeImmutable::class)]
    public ?\DateTimeImmutable $returnedAt = null;
}
