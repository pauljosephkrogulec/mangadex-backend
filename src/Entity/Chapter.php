<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'chapter')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['chapter:read:collection']],
            paginationItemsPerPage: 10
        ),
        new Get(
            normalizationContext: ['groups' => ['chapter:read:item']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['chapter:write:create']],
            normalizationContext: ['groups' => ['chapter:read:item']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['chapter:write:update']],
            normalizationContext: ['groups' => ['chapter:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['chapter:write:patch']],
            normalizationContext: ['groups' => ['chapter:read:item']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getUploader() == user"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'volume' => 'partial',
    'chapter' => 'partial',
    'translatedLanguage' => 'exact',
    'manga.id' => 'exact',
    'uploader.id' => 'exact',
    'groups.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'publishAt', 'readableAt', 'volume', 'chapter'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt', 'publishAt', 'readableAt'])]
class Chapter
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['chapter:read:collection', 'chapter:read:item'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    #[Assert\Length(max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    private ?string $volume = null;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    #[Assert\Length(max: 8)]
    private ?string $chapter = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['chapter:read:collection', 'chapter:read:item'])]
    private int $pages = 0;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Regex(pattern: '/^[a-z]{2}(-[a-z]{2})?$/')]
    private string $translatedLanguage;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    private User $uploader;

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    #[Assert\Url]
    #[Assert\Length(max: 512)]
    private ?string $externalUrl = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['chapter:read:collection', 'chapter:read:item'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['chapter:read:collection', 'chapter:read:item'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    private ?\DateTimeImmutable $publishAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['chapter:read:collection', 'chapter:read:item'])]
    private ?\DateTimeImmutable $readableAt = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['chapter:read:collection', 'chapter:read:item'])]
    private bool $isUnavailable = false;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'chapters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    private Manga $manga;

    #[ORM\ManyToMany(targetEntity: ScanlationGroup::class, inversedBy: 'chapters')]
    #[ORM\JoinTable(name: 'chapter_scanlation_groups')]
    #[Groups(['chapter:read:collection', 'chapter:read:item', 'chapter:write:create', 'chapter:write:update', 'chapter:write:patch'])]
    private Collection $groups;

    #[ORM\OneToMany(mappedBy: 'chapter', targetEntity: Report::class, cascade: ['remove'])]
    private Collection $reports;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
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

    public function getChapter(): ?string
    {
        return $this->chapter;
    }

    public function setChapter(?string $chapter): self
    {
        $this->chapter = $chapter;
        return $this;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function setPages(int $pages): self
    {
        $this->pages = $pages;
        return $this;
    }

    public function getTranslatedLanguage(): string
    {
        return $this->translatedLanguage;
    }

    public function setTranslatedLanguage(string $translatedLanguage): self
    {
        $this->translatedLanguage = $translatedLanguage;
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

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function setExternalUrl(?string $externalUrl): self
    {
        $this->externalUrl = $externalUrl;
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

    public function getPublishAt(): ?\DateTimeImmutable
    {
        return $this->publishAt;
    }

    public function setPublishAt(?\DateTimeImmutable $publishAt): self
    {
        $this->publishAt = $publishAt;
        return $this;
    }

    public function getReadableAt(): ?\DateTimeImmutable
    {
        return $this->readableAt;
    }

    public function setReadableAt(?\DateTimeImmutable $readableAt): self
    {
        $this->readableAt = $readableAt;
        return $this;
    }

    public function isUnavailable(): bool
    {
        return $this->isUnavailable;
    }

    public function setIsUnavailable(bool $isUnavailable): self
    {
        $this->isUnavailable = $isUnavailable;
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

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(ScanlationGroup $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }
        return $this;
    }

    public function removeGroup(ScanlationGroup $group): self
    {
        $this->groups->removeElement($group);
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
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }
}
