<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\Input\UserCreateInput;
use App\Entity\User;
use App\State\Processor\UserCreateProcessor;
use App\State\Provider\UserProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            input: UserCreateInput::class,
            processor: UserCreateProcessor::class
        ),
        new GetCollection(provider: UserProvider::class),
        new Get(provider: UserProvider::class),
    ],
    normalizationContext: ['groups' => ['user:read']],
)]
#[Map(source: User::class)]
class UserResource
{
    #[ApiProperty(identifier: true)]
    #[Groups(['user:read'])]
    public int $id;

    #[Groups(['user:read'])]
    public string $firstName;

    #[Groups(['user:read'])]
    public string $lastName;

    #[Groups(['user:read'])]
    public string $email;

    #[Groups(['user:read'])]
    public string $photoUrl;
}
