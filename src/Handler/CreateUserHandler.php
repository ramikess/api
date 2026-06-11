<?php

declare(strict_types=1);

namespace App\Handler;

use App\Dto\Input\UserCreateInput;
use App\Entity\User;
use App\Service\FileUploaderService;

readonly class CreateUserHandler
{
    public function __construct(
        private FileUploaderService $fileUploaderService,
    ) {}

    public function createUser(UserCreateInput $data): User
    {
        $user = new User();
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);
        $user->setPhoto($this->fileUploaderService->upload($data->photo));

        return $user;
    }
}
