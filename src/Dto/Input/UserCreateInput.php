<?php

declare(strict_types=1);

namespace App\Dto\Input;

use App\Validator\Constraints\UniqueEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['UserCreateInput', 'create'])]
class UserCreateInput
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $lastName;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[UniqueEmail(groups: ['create'])]
    public string $email;

    #[Assert\NotNull]
    #[Assert\Image]
    public ?UploadedFile $photo = null;
}
