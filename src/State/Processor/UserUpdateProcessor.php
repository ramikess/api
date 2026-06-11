<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserResource;
use App\Dto\Input\UserUpdateInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class UserUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
        private RequestStack $requestStack,
    ) {}

    /**
     * @param UserUpdateInput $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['id']);

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);

        $this->entityManager->flush();

        $resource = $this->objectMapper->map($user, UserResource::class);
        $baseUrl = $this->requestStack->getCurrentRequest()?->getSchemeAndHttpHost();
        $resource->photoUrl = $user->getPhoto()
            ? $baseUrl . '/uploads/users/' . $user->getPhoto()
            : null;

        return $resource;
    }
}
