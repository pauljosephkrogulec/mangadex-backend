<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'cover_art')]
#[ApiResource(
    normalizationContext: ['groups' => ['cover_art:read']],
    denormalizationContext: ['groups' => ['cover_art:write']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'manga.id' => 'exact',
    'uploader.id' => 'exact',
    'volume' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'volume'])]
class CoverArt
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['cover_art:read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['cover_art:read', 'cover_art:write'])]
    private ?string $volume = null;

    #[ORM\Column(type: 'string', length: 512)]
    #[Groups(['cover_art:read', 'cover_art:write'])]
    #[Assert\NotBlank]
    #[Assert\Url]
    private string $fileName;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['cover_art:read', 'cover_art:write'])]
    private ?string $locale = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['cover_art:read', 'cover_art:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cover_art:read', 'cover_art:write'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['cover_art:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['cover_art:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'coverArts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cover_art:read', 'cover_art:write'])]
    private Manga $manga;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cover_art:read', 'cover_art:write'])]
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
            if ($report->getObjectId() === $this->id) {
                $report->setObjectId(null);
            }
        }
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }
}
