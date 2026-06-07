<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserResource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // GetCollection
        if ($operation instanceof GetCollection) {
            $users = $this->entityManager->getRepository(User::class)->findAll();

            return array_map(fn(User $user) => $this->toResource($user), $users);
        }

        // Get
        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['id']);

        if ($user === null) {
            return null;
        }

        return $this->toResource($user);
    }

    private function toResource(User $user): UserResource
    {
        $resource = new UserResource();
        $resource->id = $user->getId();
        $resource->firstName = $user->getFirstName();
        $resource->lastName = $user->getLastName();
        $resource->email = $user->getEmail();
        $resource->photoUrl = $user->getPhoto() ? '/uploads/users/' . $user->getPhoto() : null;

        return $resource;
    }
}
