<?php

declare(strict_types=1);

namespace App\Dto\Input;

use App\Validator\Constraints\UniqueEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateInput
{
    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotBlank]
    public string $lastName;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[UniqueEmail(groups: ['create'])]
    public string $email;

    #[Assert\NotNull]
    #[Assert\Image]
    public UploadedFile $photo;
}
