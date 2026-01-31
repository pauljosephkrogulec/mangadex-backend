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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'manga_recommendation')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['manga_recommendation:read:collection']],
            paginationItemsPerPage: 10
        ),
        new Get(
            normalizationContext: ['groups' => ['manga_recommendation:read:item']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['manga_recommendation:write:create']],
            normalizationContext: ['groups' => ['manga_recommendation:read:item']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['manga_recommendation:write:update']],
            normalizationContext: ['groups' => ['manga_recommendation:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['manga_recommendation:write:patch']],
            normalizationContext: ['groups' => ['manga_recommendation:read:item']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'manga.id' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'score'])]
class MangaRecommendation
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['manga_recommendation:read:collection', 'manga_recommendation:read:item'])]
    private ?string $id = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['manga_recommendation:read:collection', 'manga_recommendation:read:item', 'manga_recommendation:write:create', 'manga_recommendation:write:update', 'manga_recommendation:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 1)]
    private float $score;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga_recommendation:read:collection', 'manga_recommendation:read:item'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga_recommendation:read:collection', 'manga_recommendation:read:item'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'recommendations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['manga_recommendation:read:collection', 'manga_recommendation:read:item', 'manga_recommendation:write:create', 'manga_recommendation:write:update', 'manga_recommendation:write:patch'])]
    private Manga $manga;

    #[ORM\ManyToOne(targetEntity: Manga::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['manga_recommendation:read:collection', 'manga_recommendation:read:item', 'manga_recommendation:write:create', 'manga_recommendation:write:update', 'manga_recommendation:write:patch'])]
    private Manga $recommendedManga;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

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

    public function getRecommendedManga(): Manga
    {
        return $this->recommendedManga;
    }

    public function setRecommendedManga(Manga $recommendedManga): self
    {
        $this->recommendedManga = $recommendedManga;

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
