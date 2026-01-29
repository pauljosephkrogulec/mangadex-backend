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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'tag')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['tag:read:collection']]
        ),
        new Get(
            normalizationContext: ['groups' => ['tag:read:item']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['tag:write:create']],
            normalizationContext: ['groups' => ['tag:read:item']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['tag:write:update']],
            normalizationContext: ['groups' => ['tag:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['tag:write:patch']],
            normalizationContext: ['groups' => ['tag:read:item']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'group' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'name'])]
class Tag
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['tag:read:collection', 'tag:read:item', 'manga:read:collection', 'manga:read:item'])]
    private ?string $id = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['tag:read:collection', 'tag:read:item', 'tag:write:create', 'tag:write:update', 'tag:write:patch', 'manga:read:item'])]
    #[Assert\NotNull]
    private array $name = [];

    #[ORM\Column(type: 'json')]
    #[Groups(['tag:read:collection', 'tag:read:item', 'tag:write:create', 'tag:write:update', 'tag:write:patch'])]
    private array $description = [];

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['tag:read:collection', 'tag:read:item', 'tag:write:create', 'tag:write:update', 'tag:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Choice(choices: ['content', 'format', 'genre', 'theme'])]
    private string $tagGroup;

    #[ORM\Column(type: 'integer')]
    #[Groups(['tag:read:collection', 'tag:read:item', 'tag:write:create', 'tag:write:update', 'tag:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['tag:read:collection', 'tag:read:item'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['tag:read:collection', 'tag:read:item'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToMany(targetEntity: Manga::class, mappedBy: 'tags')]
    private Collection $manga;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: Report::class, cascade: ['remove'])]
    private Collection $reports;

    public function __construct()
    {
        $this->manga = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): array
    {
        return $this->name;
    }

    public function setName(array $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getTagGroup(): string
    {
        return $this->tagGroup;
    }

    public function setTagGroup(string $tagGroup): self
    {
        $this->tagGroup = $tagGroup;
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

    public function getManga(): Collection
    {
        return $this->manga;
    }

    public function addManga(Manga $manga): self
    {
        if (!$this->manga->contains($manga)) {
            $this->manga->add($manga);
            $manga->addTag($this);
        }
        return $this;
    }

    public function removeManga(Manga $manga): self
    {
        if ($this->manga->removeElement($manga)) {
            $manga->removeTag($this);
        }
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
