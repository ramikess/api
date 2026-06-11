<?php

declare(strict_types=1);

namespace App\Serializer\Denormalizer;

use App\Dto\Input\UserCreateInput;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UserCreateInputDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(private RequestStack $requestStack) {}

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $input = new UserCreateInput();
        $input->firstName = $data['firstName'] ?? '';
        $input->lastName = $data['lastName'] ?? '';
        $input->email = $data['email'] ?? '';
        $input->photo = $this->requestStack->getCurrentRequest()->files->get('photo');

        return $input;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === UserCreateInput::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [UserCreateInput::class => true];
    }
}
