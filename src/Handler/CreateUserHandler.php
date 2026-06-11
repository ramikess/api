<?php

declare(strict_types=1);

namespace App\Handler;

use App\Dto\Input\UserCreateInput;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class CreateUserHandler
{
    public function __construct(
        private SluggerInterface       $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/users')]
        private string                 $uploadDir,
    ) {}

    public function createUser(UserCreateInput $data): User
    {
        $user = new User();
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);
        $user->setPhoto($this->handleUpload($data->photo));

        return $user;
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
