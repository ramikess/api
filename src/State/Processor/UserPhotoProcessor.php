<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserResource;
use App\Dto\Input\UserPhotoInput;
use App\Entity\User;
use App\Service\FileUploader;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class UserPhotoProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
        private RequestStack $requestStack,
        private FileUploaderService $fileUploaderService,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        $request = $this->requestStack->getCurrentRequest();
        $file = $request->files->get('photo');

        if ($file === null) {
            throw new \InvalidArgumentException('Photo file is required.');
        }

        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['id']);

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        if ($user->getPhoto()) {
            $this->fileUploaderService->remove($user->getPhoto());
        }

        $user->setPhoto($this->fileUploaderService->upload($file));
        $this->entityManager->flush();

        return $this->objectMapper->map($user, UserResource::class);
    }
}
