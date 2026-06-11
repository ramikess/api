<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\LoanRepository;

final readonly class LoanDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private LoanRepository $loanRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        $loan = $this->loanRepository->find($uriVariables['id']);

        if (!$loan) {
            throw new \RuntimeException('Loan not found');
        }

        $this->loanRepository->remove($loan, true);

        return null;
    }
}
