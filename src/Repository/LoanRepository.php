<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\User;
use App\Enum\LoanStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    /**
     * @return Loan[]
     */
    public function findByFilters(?int $userId = null, ?LoanStatus $status = null): array
    {
        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC');

        if ($userId !== null) {
            $qb->andWhere('IDENTITY(l.user) = :userId')
                ->setParameter('userId', $userId);
        }

        if ($status !== null) {
            $qb->andWhere('l.status = :status')
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    // LoanRepository.php
    public function hasOverlappingLoan(Book $book, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): bool
    {
        return (bool) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.book = :book')
            ->andWhere('l.status = :status')
            ->andWhere('l.startDate < :endDate')
            ->andWhere('l.endDate > :startDate')
            ->setParameter('book', $book)
            ->setParameter('status', LoanStatus::Active)
            ->setParameter('endDate', $endDate)
            ->setParameter('startDate', $startDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countActiveLoansForUser(User $user): int
    {
        return $this->count(['user' => $user, 'status' => LoanStatus::Active]);
    }
}
