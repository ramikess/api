<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\LoanResource;
use App\Entity\Loan;
use App\Enum\LoanStatus;
use App\Mapper\LoanResourceMapper;
use App\Repository\LoanRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<LoanResource>
 */
readonly class LoanProvider implements ProviderInterface
{
    public function __construct(
        private LoanRepository     $loanRepository,
        private LoanResourceMapper $mapper,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        if (isset($uriVariables['id'])) {
            return $this->provideOne((int) $uriVariables['id']);
        }

        return $this->provideCollection($context);
    }

    private function provideOne(int $id): LoanResource
    {
        $loan = $this->loanRepository->find($id);

        if (!$loan instanceof Loan) {
            throw new NotFoundHttpException(sprintf('Emprunt #%d introuvable.', $id));
        }

        if (isset($uriVariables['userId'])) {
            return $this->provideCollectionByUser((int) $uriVariables['userId']);
        }

        return $this->mapper->map($loan);
    }

    /**
     * @return array<LoanResource>
     */
    private function provideCollectionByUser(int $userId): array
    {
        return array_map(
            fn(Loan $loan) => $this->mapper->map($loan),
            $this->loanRepository->findBy(['user' => $userId])
        );
    }

    private function provideCollection(array $context): array
    {
        $filters = $context['filters'] ?? [];

        $userId = isset($filters['userId']) ? (int) $filters['userId'] : null;
        $status = isset($filters['status']) ? LoanStatus::tryFrom($filters['status']) : null;

        return array_map(
            fn(Loan $loan) => $this->mapper->map($loan),
            $this->loanRepository->findByFilters($userId, $status)
        );
    }
}
