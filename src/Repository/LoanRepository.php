<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Loan;
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
}
