<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'cover_art')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['cover_art:read:collection']],
            paginationItemsPerPage: 10
        ),
        new Get(
            normalizationContext: ['groups' => ['cover_art:read:item']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['cover_art:write:create']],
            normalizationContext: ['groups' => ['cover_art:read:item']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['cover_art:write:update']],
            normalizationContext: ['groups' => ['cover_art:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['cover_art:write:patch']],
            normalizationContext: ['groups' => ['cover_art:read:item']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getUploader() == user"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'manga.id' => 'exact',
    'uploader.id' => 'exact',
    'volume' => 'partial',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'volume'])]
class CoverArt
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item', 'cover_art:write:create', 'cover_art:write:update', 'cover_art:write:patch'])]
    private ?string $volume = null;

    #[ORM\Column(type: 'string', length: 512)]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item', 'cover_art:write:create', 'cover_art:write:update', 'cover_art:write:patch', 'manga:read:collection', 'manga:read:item'])]
    #[Assert\NotBlank]
    #[Assert\Url]
    private string $fileName;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item', 'cover_art:write:create', 'cover_art:write:update', 'cover_art:write:patch'])]
    private ?string $locale = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item', 'cover_art:write:create', 'cover_art:write:update', 'cover_art:write:patch'])]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item', 'cover_art:write:create', 'cover_art:write:update', 'cover_art:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'coverArts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item', 'cover_art:write:create', 'cover_art:write:update', 'cover_art:write:patch'])]
    private Manga $manga;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cover_art:read:collection', 'cover_art:read:item', 'cover_art:write:create', 'cover_art:write:update', 'cover_art:write:patch'])]
    private User $uploader;

    #[ORM\OneToMany(mappedBy: 'coverArt', targetEntity: Report::class, cascade: ['remove'])]
    private Collection $reports;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(?string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getManga(): Manga
    {
        return $this->manga;
    }

    public function setManga(Manga $manga): self
    {
        $this->manga = $manga;

        return $this;
    }

    public function getUploader(): User
    {
        return $this->uploader;
    }

    public function setUploader(User $uploader): self
    {
        $this->uploader = $uploader;

        return $this;
    }

    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setObjectId($this->id);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // Don't set objectId to null as it's a required field in Report entity
            // The report should be handled differently if needed
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }
}
