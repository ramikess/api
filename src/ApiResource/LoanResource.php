<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\Input\LoanCreateInput;
use App\Dto\Input\LoanReturnInput;
use App\Entity\Loan;
use App\State\Processor\LoanCreateProcessor;
use App\State\Processor\LoanReturnProcessor;
use App\State\Provider\LoanProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'Loan',
    operations: [
        new Post(
            uriTemplate: '/loans',
            inputFormats: ['json'],
            normalizationContext: ['groups' => ['loan:read']],
            input: LoanCreateInput::class,
            processor: LoanCreateProcessor::class,
        ),
        new Post(
            uriTemplate: '/loans/return',
            normalizationContext: ['groups' => ['loan:read']],
            input: LoanReturnInput::class,
            processor: LoanReturnProcessor::class,
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['loan:read']],
            provider: LoanProvider::class,
        ),
        new Get(
            normalizationContext: ['groups' => ['loan:read']],
            provider: LoanProvider::class,
        ),
    ],
)]
class LoanResource
{
    #[ApiProperty(identifier: true)]
    #[Groups(['loan:read'])]
    public int $id;

    #[Groups(['loan:read'])]
    public array $user;

    #[Groups(['loan:read'])]
    public array $book;

    #[Groups(['loan:read'])]
    public \DateTimeImmutable $startDate;

    #[Groups(['loan:read'])]
    public \DateTimeImmutable $endDate;

    #[Groups(['loan:read'])]
    public ?\DateTimeImmutable $returnedAt;

    #[Map(source: 'status', transform: [self::class, 'extractStatusValue'])]
    #[Groups(['loan:read'])]
    public string $status;

    #[Map(source: 'status', transform: [self::class, 'extractStatusLabel'])]
    #[Groups(['loan:read'])]
    public string $statusLabel;

    #[Groups(['loan:read'])]
    public bool $isOverdue;

    #[Groups(['loan:details'])]
    public ?int $daysRemaining = null;

    #[Groups(['loan:details'])]
    public ?int $daysLate = null;

    public static function extractStatusValue(mixed $status): string
    {
        return $status->value;
    }

    public static function extractStatusLabel(mixed $status): string
    {
        return $status->getLabel();
    }
}
