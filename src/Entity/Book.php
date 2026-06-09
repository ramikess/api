<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\BookRepository;
use App\State\Provider\AvailableBookProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['book:read']],
    denormalizationContext: ['groups' => ['book:write']],
)]
#[Vich\Uploadable]
#[GetCollection(
    uriTemplate: '/books/available',
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['book:read']],
    provider: AvailableBookProvider::class,
)]
#[GetCollection(
    uriTemplate: '/books',
    outputFormats: ['json' => ['application/json']]
)]
#[Get(
    uriTemplate: '/books/{id}',
    outputFormats: ['json' => ['application/json']],
    requirements: ['id' => '\d+'],
)]
#[Post(
    inputFormats: ['multipart' => ['multipart/form-data']],
    outputFormats: ['json' => ['application/json']]
)]
#[Put(
    inputFormats: ['multipart' => ['multipart/form-data']],
    outputFormats: ['json' => ['application/json']]
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['book:read', 'book:write'])]
    private string $title;

    #[ORM\Column(length: 255)]
    #[Groups(['book:read', 'book:write'])]
    private string $description;

    #[ORM\Column(length: 255, nullable: true)]
    #[ApiProperty(writable: false)]
    #[Groups(['book:read'])]
    private ?string $imageName = null;

    #[Vich\UploadableField(mapping: 'book_images', fileNameProperty: 'imageName')]
    #[Groups(['book:write'])]
    private ?File $imageFile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(updatable: false)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: Loan::class, mappedBy: 'book')]
    private Collection $loans;

    public function __construct()
    {
        $this->loans = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function isAvailable(): bool
    {
        foreach ($this->loans as $loan) {
            if ($loan->isActive()) {
                return false;
            }
        }
        return true;
    }
}
