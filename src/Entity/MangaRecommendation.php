<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'manga_recommendation')]
#[ApiResource(
    normalizationContext: ['groups' => ['manga_recommendation:read']],
    denormalizationContext: ['groups' => ['manga_recommendation:write']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'manga.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'score'])]
class MangaRecommendation
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['manga_recommendation:read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['manga_recommendation:read', 'manga_recommendation:write'])]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 1)]
    private float $score;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga_recommendation:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga_recommendation:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'recommendations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['manga_recommendation:read', 'manga_recommendation:write'])]
    private Manga $manga;

    #[ORM\ManyToOne(targetEntity: Manga::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['manga_recommendation:read', 'manga_recommendation:write'])]
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
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }
}
