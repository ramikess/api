<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserResource;
use App\Dto\Input\UserCreateInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class UserCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SluggerInterface       $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/users')]
        private string                 $uploadDir,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        /** @var UserCreateInput $data */
        $user = new User();
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);
        $user->setPhoto($this->handleUpload($data->photo));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $resource = new UserResource();
        $resource->id = $user->getId();
        $resource->firstName = $user->getFirstName();
        $resource->lastName = $user->getLastName();
        $resource->email = $user->getEmail();
        $resource->photoUrl = '/uploads/users/' . $user->getPhoto();

        return $resource;
    }

    private function handleUpload(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $this->slugger->slug($originalName);
        $fileName = $safeName . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->uploadDir, $fileName);

        return $fileName;
    }
}
