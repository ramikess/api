<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserResource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class UserProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            $users = $this->entityManager->getRepository(User::class)->findAll();

            return array_map(
                fn(User $user) => $this->objectMapper->map($user, UserResource::class),
                $users,
            );
        }

        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['id']);

        if ($user === null) {
            return null;
        }

        return $this->objectMapper->map($user, UserResource::class);
    }
}
