<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\Input\UserCreateInput;
use App\Dto\Input\UserPhotoInput;
use App\Dto\Input\UserUpdateInput;
use App\Entity\User;
use App\State\Processor\UserCreateProcessor;
use App\State\Processor\UserPhotoProcessor;
use App\State\Processor\UserUpdateProcessor;
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
        new Put(
            input: UserUpdateInput::class,
            provider: UserProvider::class,
            processor: UserUpdateProcessor::class,
        ),
        new Post(
            uriTemplate: '/users/{id}/photo',
            inputFormats: ['multipart' => ['multipart/form-data']],
            provider: UserProvider::class,
            processor: UserPhotoProcessor::class,
        ),
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
    #[Map(source: 'photo')]
    public string $photoUrl;
}
