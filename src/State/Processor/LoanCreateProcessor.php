<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\LoanResource;
use App\Dto\Input\LoanCreateInput;
use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\User;
use App\Enum\LoanStatus;
use App\Mapper\LoanResourceMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<LoanCreateInput, LoanResource>
 */
readonly class LoanCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoanResourceMapper     $mapper,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): LoanResource
    {
        if (!$data instanceof LoanCreateInput) {
            throw new \RuntimeException(sprintf(
                'Expected %s, got %s.',
                LoanCreateInput::class,
                get_debug_type($data)
            ));
        }

        $user = $this->resolveUser($data->userId);
        $book = $this->resolveBook($data->bookId);

        $this->assertBookAvailable($book);
        $this->assertUserLoanQuota($user);

        $loan = $this->createLoan($user, $book, $data);

        $this->entityManager->persist($loan);
        $this->entityManager->flush();

        return $this->mapper->map($loan);
    }

    private function resolveUser(int $userId): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException(sprintf('Utilisateur #%d introuvable.', $userId));
        }

        return $user;
    }

    private function resolveBook(int $bookId): Book
    {
        $book = $this->entityManager->getRepository(Book::class)->find($bookId);

        if (!$book instanceof Book) {
            throw new NotFoundHttpException(sprintf('Livre #%d introuvable.', $bookId));
        }

        return $book;
    }

    private function assertBookAvailable(Book $book): void
    {
        $activeLoan = $this->entityManager->getRepository(Loan::class)->findOneBy([
            'book'   => $book,
            'status' => LoanStatus::Active,
        ]);

        if ($activeLoan instanceof Loan) {
            throw new BadRequestHttpException(sprintf(
                'Le livre "%s" est déjà emprunté.',
                $book->getTitle()
            ));
        }
    }

    private function assertUserLoanQuota(User $user): void
    {
        $activeLoansCount = $this->entityManager->getRepository(Loan::class)->count([
            'user'   => $user,
            'status' => LoanStatus::Active,
        ]);

        if ($activeLoansCount >= 3) {
            throw new BadRequestHttpException(sprintf(
                'L\'utilisateur "%s %s" a atteint la limite de 3 emprunts actifs.',
                $user->getFirstName(),
                $user->getLastName()
            ));
        }
    }

    private function createLoan(User $user, Book $book, LoanCreateInput $input): Loan
    {
        $loan = new Loan();
        $loan->setUser($user);
        $loan->setBook($book);
        $loan->setStartDate($input->startDate);
        $loan->setEndDate($input->endDate);

        return $loan;
    }
}
