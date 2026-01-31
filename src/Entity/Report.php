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
#[ORM\Table(name: 'report')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['report:read:collection']],
            paginationItemsPerPage: 10
        ),
        new Get(
            normalizationContext: ['groups' => ['report:read:item']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['report:write:create']],
            normalizationContext: ['groups' => ['report:read:item']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['report:write:update']],
            normalizationContext: ['groups' => ['report:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['report:write:patch']],
            normalizationContext: ['groups' => ['report:read:item']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getCreator() == user"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'objectId' => 'exact',
    'creator.id' => 'exact',
    'status' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class Report
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['report:read:collection', 'report:read:item'])]
    private ?string $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['report:read:collection', 'report:read:item', 'report:write:create', 'report:write:update', 'report:write:patch'])]
    #[Assert\NotBlank]
    private string $details;

    #[ORM\Column(type: 'guid')]
    #[Groups(['report:read:collection', 'report:read:item', 'report:write:create', 'report:write:update', 'report:write:patch'])]
    #[Assert\NotBlank]
    private string $objectId;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['report:read:collection', 'report:read:item', 'report:write:create', 'report:write:update', 'report:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Choice(choices: ['waiting', 'accepted', 'refused', 'autoresolved'])]
    private string $status = 'waiting';

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['report:read:collection', 'report:read:item'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['report:read:collection', 'report:read:item', 'report:write:create', 'report:write:update', 'report:write:patch'])]
    private User $creator;

    #[ORM\ManyToOne(targetEntity: Chapter::class, inversedBy: 'reports')]
    private ?Chapter $chapter = null;

    #[ORM\ManyToOne(targetEntity: Manga::class, inversedBy: 'reports')]
    private ?Manga $manga = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'reports')]
    private ?Author $author = null;

    #[ORM\ManyToOne(targetEntity: ScanlationGroup::class, inversedBy: 'reports')]
    private ?ScanlationGroup $scanlationGroup = null;

    #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: 'reports')]
    private ?Tag $tag = null;

    #[ORM\ManyToOne(targetEntity: CoverArt::class, inversedBy: 'reports')]
    private ?CoverArt $coverArt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function setObjectId(string $objectId): self
    {
        $this->objectId = $objectId;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): self
    {
        $this->chapter = $chapter;

        return $this;
    }

    public function getManga(): ?Manga
    {
        return $this->manga;
    }

    public function setManga(?Manga $manga): self
    {
        $this->manga = $manga;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getScanlationGroup(): ?ScanlationGroup
    {
        return $this->scanlationGroup;
    }

    public function setScanlationGroup(?ScanlationGroup $scanlationGroup): self
    {
        $this->scanlationGroup = $scanlationGroup;

        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getCoverArt(): ?CoverArt
    {
        return $this->coverArt;
    }

    public function setCoverArt(?CoverArt $coverArt): self
    {
        $this->coverArt = $coverArt;

        return $this;
    }
}
