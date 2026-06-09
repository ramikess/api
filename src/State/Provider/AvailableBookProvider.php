<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book;
use App\Entity\Loan;
use App\Enum\LoanStatus;
use Doctrine\ORM\EntityManagerInterface;

class AvailableBookProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $borrowedBookIds = $this->entityManager
            ->getRepository(Loan::class)
            ->createQueryBuilder('l')
            ->select('IDENTITY(l.book)')
            ->where('l.status = :status')
            ->setParameter('status', LoanStatus::Active)
            ->getQuery()
            ->getSingleColumnResult();

        $qb = $this->entityManager
            ->getRepository(Book::class)
            ->createQueryBuilder('b')
            ->orderBy('b.title', 'ASC');

        if (!empty($borrowedBookIds)) {
            $qb->where('b.id NOT IN (:ids)')
                ->setParameter('ids', $borrowedBookIds);
        }

        return $qb->getQuery()->getResult();
    }
}
