<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\LoanResource;
use App\Dto\Input\LoanReturnInput;
use App\Entity\Loan;
use App\Enum\LoanStatus;
use App\Mapper\LoanResourceMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<LoanReturnInput, LoanResource>
 */
final class LoanReturnProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoanResourceMapper $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): LoanResource
    {
        if (!$data instanceof LoanReturnInput) {
            throw new \RuntimeException(sprintf(
                'Expected %s, got %s.',
                LoanReturnInput::class,
                get_debug_type($data)
            ));
        }

        $loan = $this->resolveLoan($data->loanId);

        $this->assertLoanIsActive($loan);

        $loan->setReturnedAt($data->returnedAt ?? new \DateTimeImmutable());
        $loan->setStatus(LoanStatus::Returned);

        $this->entityManager->flush();

        return $this->mapper->map($loan);
    }

    private function resolveLoan(int $loanId): Loan
    {
        $loan = $this->entityManager->getRepository(Loan::class)->find($loanId);

        if (!$loan instanceof Loan) {
            throw new NotFoundHttpException(sprintf('Emprunt #%d introuvable.', $loanId));
        }

        return $loan;
    }

    private function assertLoanIsActive(Loan $loan): void
    {
        if ($loan->getStatus() !== LoanStatus::Active) {
            throw new BadRequestHttpException(sprintf(
                'L\'emprunt #%d ne peut pas être retourné (statut actuel : %s).',
                $loan->getId(),
                $loan->getStatus()->getLabel()
            ));
        }
    }
}
