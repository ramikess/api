<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserResource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class UserProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
        private RequestStack $requestStack,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {
            $users = $this->entityManager->getRepository(User::class)->findAll();

            return array_map(
                fn(User $user) => $this->toResource($user),
                $users,
            );
        }

        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['id']);

        if ($user === null) {
            return null;
        }

        return $this->toResource($user);
    }

    private function toResource(User $user): UserResource
    {
        $resource = $this->objectMapper->map($user, UserResource::class);
        $baseUrl = $this->requestStack->getCurrentRequest()?->getSchemeAndHttpHost();

        $resource->photoUrl = $user->getPhoto()
            ? $baseUrl . '/uploads/users/' . $user->getPhoto()
            : null;

        return $resource;
    }
}
