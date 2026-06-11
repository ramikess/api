<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserResource;
use App\Dto\Input\UserCreateInput;
use App\Entity\User;
use App\Service\CreateUserHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class UserCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface  $objectMapper,
        private CreateUserHandler      $createUserHandler,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface     $persistProcessor,

    ) {}

    /**
     * @param UserCreateInput $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        $user = $this->createUserHandler->createUser($data);
        $user = $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        return $this->objectMapper->map($user, UserResource::class);
    }
}
