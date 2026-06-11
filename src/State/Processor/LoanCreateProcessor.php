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
use App\Handler\CreateLoanHandler;
use App\Mapper\LoanResourceMapper;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Validator\Loan\LoanValidationChain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<LoanCreateInput, LoanResource>
 */
final readonly class LoanCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository      $userRepository,
        private BookRepository      $bookRepository,
        private LoanValidationChain $validationChain,
        private LoanResourceMapper  $mapper,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface  $persistProcessor,
        private CreateLoanHandler   $createLoanHandler,
    ) {}

    /**
     * @param LoanCreateInput $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): LoanResource
    {
        $user = $this->userRepository->find($data->userId)
            ?? throw EntityNotFoundException::for(User::class, $data->userId);

        $book = $this->bookRepository->find($data->bookId)
            ?? throw EntityNotFoundException::for(Book::class, $data->bookId);

        $this->validationChain->validate($user, $book, $data);

        $loan = $this->createLoanHandler->createLoan($data);
        $loan = $this->persistProcessor->process($loan, $operation, $uriVariables, $context);

        return $this->mapper->map($loan);
    }
}
