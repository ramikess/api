<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\LoanResource;
use App\Dto\Input\LoanCreateInput;
use App\Entity\Loan;
use App\Entity\User;
use App\Entity\Book;
use App\Exception\EntityNotFoundException;
use App\Mapper\LoanResourceMapper;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Validator\Loan\LoanValidationChain;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @implements ProcessorInterface<LoanCreateInput, LoanResource>
 */
final readonly class LoanCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository         $userRepository,
        private BookRepository         $bookRepository,
        private EntityManagerInterface  $entityManager,
        private LoanValidationChain    $validationChain,
        private LoanResourceMapper     $mapper,
    ) {}

    /**
     * @param LoanCreateInput $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): LoanResource
    {
        $user = $this->userRepository->find($data->userId)
            ?? throw EntityNotFoundException::for('Utilisateur', $data->userId);

        $book = $this->bookRepository->find($data->bookId)
            ?? throw EntityNotFoundException::for('Livre', $data->bookId);

        $this->validationChain->validate($user, $book, $data);

        $loan = new Loan();
        $loan->setUser($user);
        $loan->setBook($book);
        $loan->setStartDate($data->startDate);
        $loan->setEndDate($data->endDate);

        $this->entityManager->persist($loan);
        $this->entityManager->flush();

        return $this->mapper->map($loan);
    }
}
