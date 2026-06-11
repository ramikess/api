<?php

declare(strict_types=1);

namespace App\Serializer\Denormalizer;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class BookMultipartDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'BOOK_MULTIPART_DENORMALIZER_ALREADY_CALLED';

    public function __construct(private readonly RequestStack $requestStack) {}

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[self::ALREADY_CALLED] = true;
        $book = $this->denormalizer->denormalize($data, $type, $format, $context);

        $request = $this->requestStack->getCurrentRequest();
        $uploadedFile = $request?->files->get('imageFile');

        if ($uploadedFile instanceof UploadedFile) {
            $book->setImageFile($uploadedFile);
            $book->setUpdatedAt(new \DateTimeImmutable());
        }

        return $book;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Book::class && !isset($context[self::ALREADY_CALLED]);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Book::class => false];
    }
}
