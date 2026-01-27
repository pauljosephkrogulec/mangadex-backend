<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attributes\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'manga_relation')]
#[ApiResource(
    normalizationContext: ['groups' => ['manga_relation:read']],
    denormalizationContext: ['groups' => ['manga_relation:write']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'manga.id' => 'exact',
    'targetManga.id' => 'exact',
    'relation' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class MangaRelation
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['manga_relation:read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['manga_relation:read', 'manga_relation:write'])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [
        'monochrome', 'main_story', 'adapted_from', 'based_on', 'prequel',
        'side_story', 'doujinshi', 'same_franchise', 'shared_universe',
        'sequel', 'spin_off', 'alternate_story', 'alternate_version',
        'preserialization', 'colored', 'serialization'
    ])]
    private string $relation;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga_relation:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga_relation:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'relations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['manga_relation:read', 'manga_relation:write'])]
    private Manga $manga;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'relatedManga')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['manga_relation:read', 'manga_relation:write'])]
    private Manga $targetManga;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'relatedManga')]
    #[ORM\JoinColumn(nullable: false)]
    private Manga $sourceManga;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): self
    {
        $this->relation = $relation;
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

    public function getTargetManga(): Manga
    {
        return $this->targetManga;
    }

    public function setTargetManga(Manga $targetManga): self
    {
        $this->targetManga = $targetManga;
        return $this;
    }

    public function getSourceManga(): Manga
    {
        return $this->sourceManga;
    }

    public function setSourceManga(Manga $sourceManga): self
    {
        $this->sourceManga = $sourceManga;
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
