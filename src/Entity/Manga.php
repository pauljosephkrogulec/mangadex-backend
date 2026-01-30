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
#[ORM\Table(name: 'manga')]
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationClientEnabled: true,
            paginationClientItemsPerPage: true,
            normalizationContext: ['groups' => ['manga:read:collection', 'cover_art:read']]
        ),
        new Get(
            normalizationContext: ['groups' => ['manga:read:item', 'cover_art:read']]
        ),
        new Post(
            normalizationContext: ['groups' => ['manga:read:item', 'cover_art:read']],
            denormalizationContext: ['groups' => ['manga:write:create']]
        ),
        new Put(
            normalizationContext: ['groups' => ['manga:read:item', 'cover_art:read']],
            denormalizationContext: ['groups' => ['manga:write:update']]
        ),
        new Patch(
            normalizationContext: ['groups' => ['manga:read:item', 'cover_art:read']],
            denormalizationContext: ['groups' => ['manga:write:patch']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'originalLanguage' => 'exact',
    'status' => 'exact',
    'contentRating' => 'exact',
    'publicationDemographic' => 'exact',
    'year' => 'exact',
    'authors.id' => 'exact',
    'artists.id' => 'exact',
    'tags.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'title', 'year'])]
#[ApiFilter(RangeFilter::class, properties: ['year'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt'])]
class Manga
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'chapter:read', 'cover_art:read', 'chapter:read:item'])]
    private ?string $id = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch', 'chapter:read:item'])]
    #[Assert\NotNull]
    private array $title = [];

    #[ORM\Column(type: 'json')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private array $altTitles = [];

    #[ORM\Column(type: 'json')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private array $description = [];

    #[ORM\Column(type: 'boolean')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private bool $isLocked = false;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private ?array $links = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private ?array $officialLinks = null;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Regex(pattern: '/^[a-z]{2}(-[a-z]{2})?$/')]
    private string $originalLanguage;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private ?string $lastVolume = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private ?string $lastChapter = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    #[Assert\Choice(choices: ['shounen', 'shoujo', 'josei', 'seinen'])]
    private ?string $publicationDemographic = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Choice(choices: ['completed', 'ongoing', 'cancelled', 'hiatus'])]
    private string $status;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    #[Assert\Range(min: 1, max: 9999)]
    private ?int $year = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Choice(choices: ['safe', 'suggestive', 'erotica', 'pornographic'])]
    private string $contentRating;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private bool $chapterNumbersResetOnNewVolume = false;

    #[ORM\Column(type: 'json')]
    #[Groups(['manga:read:collection', 'manga:read:item'])]
    private array $availableTranslatedLanguages = [];

    #[ORM\Column(type: 'guid', nullable: true)]
    #[Groups(['manga:read:collection', 'manga:read:item'])]
    private ?string $latestUploadedChapter = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    #[Assert\Choice(choices: ['draft', 'submitted', 'published', 'rejected'])]
    private string $state = 'draft';

    #[ORM\Column(type: 'integer')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga:read:collection', 'manga:read:item'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['manga:read:collection', 'manga:read:item'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'mangaAsAuthor')]
    #[ORM\JoinTable(name: 'manga_authors')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private Collection $authors;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'mangaAsArtist')]
    #[ORM\JoinTable(name: 'manga_artists')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private Collection $artists;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'manga')]
    #[ORM\JoinTable(name: 'manga_tags')]
    #[Groups(['manga:read:collection', 'manga:read:item', 'manga:write:create', 'manga:write:update', 'manga:write:patch'])]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'manga', targetEntity: Chapter::class, cascade: ['remove'])]
    private Collection $chapters;

    #[ORM\OneToMany(mappedBy: 'manga', targetEntity: CoverArt::class, cascade: ['remove'])]
    #[Groups(['manga:read:collection', 'manga:read:item'])]
    private Collection $coverArts;

    #[ORM\OneToMany(mappedBy: 'manga', targetEntity: MangaRelation::class, cascade: ['remove'])]
    private Collection $relations;

    #[ORM\OneToMany(mappedBy: 'sourceManga', targetEntity: MangaRelation::class, cascade: ['remove'])]
    private Collection $relatedManga;

    #[ORM\OneToMany(mappedBy: 'manga', targetEntity: MangaRecommendation::class, cascade: ['remove'])]
    private Collection $recommendations;

    #[ORM\ManyToMany(targetEntity: CustomList::class, mappedBy: 'manga')]
    private Collection $customLists;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'followedManga')]
    private Collection $followers;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->chapters = new ArrayCollection();
        $this->coverArts = new ArrayCollection();
        $this->relations = new ArrayCollection();
        $this->relatedManga = new ArrayCollection();
        $this->recommendations = new ArrayCollection();
        $this->customLists = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function setTitle(array $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getAltTitles(): array
    {
        return $this->altTitles;
    }

    public function setAltTitles(array $altTitles): self
    {
        $this->altTitles = $altTitles;
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

    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;
        return $this;
    }

    public function getLinks(): ?array
    {
        return $this->links;
    }

    public function setLinks(?array $links): self
    {
        $this->links = $links;
        return $this;
    }

    public function getOfficialLinks(): ?array
    {
        return $this->officialLinks;
    }

    public function setOfficialLinks(?array $officialLinks): self
    {
        $this->officialLinks = $officialLinks;
        return $this;
    }

    public function getOriginalLanguage(): string
    {
        return $this->originalLanguage;
    }

    public function setOriginalLanguage(string $originalLanguage): self
    {
        $this->originalLanguage = $originalLanguage;
        return $this;
    }

    public function getLastVolume(): ?string
    {
        return $this->lastVolume;
    }

    public function setLastVolume(?string $lastVolume): self
    {
        $this->lastVolume = $lastVolume;
        return $this;
    }

    public function getLastChapter(): ?string
    {
        return $this->lastChapter;
    }

    public function setLastChapter(?string $lastChapter): self
    {
        $this->lastChapter = $lastChapter;
        return $this;
    }

    public function getPublicationDemographic(): ?string
    {
        return $this->publicationDemographic;
    }

    public function setPublicationDemographic(?string $publicationDemographic): self
    {
        $this->publicationDemographic = $publicationDemographic;
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

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getContentRating(): string
    {
        return $this->contentRating;
    }

    public function setContentRating(string $contentRating): self
    {
        $this->contentRating = $contentRating;
        return $this;
    }

    public function isChapterNumbersResetOnNewVolume(): bool
    {
        return $this->chapterNumbersResetOnNewVolume;
    }

    public function setChapterNumbersResetOnNewVolume(bool $chapterNumbersResetOnNewVolume): self
    {
        $this->chapterNumbersResetOnNewVolume = $chapterNumbersResetOnNewVolume;
        return $this;
    }

    public function getAvailableTranslatedLanguages(): array
    {
        return $this->availableTranslatedLanguages;
    }

    public function setAvailableTranslatedLanguages(array $availableTranslatedLanguages): self
    {
        $this->availableTranslatedLanguages = $availableTranslatedLanguages;
        return $this;
    }

    public function getLatestUploadedChapter(): ?string
    {
        return $this->latestUploadedChapter;
    }

    public function setLatestUploadedChapter(?string $latestUploadedChapter): self
    {
        $this->latestUploadedChapter = $latestUploadedChapter;
        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;
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

    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);
        return $this;
    }

    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Author $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
        }
        return $this;
    }

    public function removeArtist(Author $artist): self
    {
        $this->artists->removeElement($artist);
        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): self
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters->add($chapter);
            $chapter->setManga($this);
        }
        return $this;
    }

    public function removeChapter(Chapter $chapter): self
    {
        if ($this->chapters->removeElement($chapter)) {
            if ($chapter->getManga() === $this) {
                $chapter->setManga(null);
            }
        }
        return $this;
    }

    public function getCoverArts(): Collection
    {
        return $this->coverArts;
    }

    public function addCoverArt(CoverArt $coverArt): self
    {
        if (!$this->coverArts->contains($coverArt)) {
            $this->coverArts->add($coverArt);
            $coverArt->setManga($this);
        }
        return $this;
    }

    public function removeCoverArt(CoverArt $coverArt): self
    {
        if ($this->coverArts->removeElement($coverArt)) {
            if ($coverArt->getManga() === $this) {
                $coverArt->setManga(null);
            }
        }
        return $this;
    }

    public function getRelations(): Collection
    {
        return $this->relations;
    }

    public function addRelation(MangaRelation $relation): self
    {
        if (!$this->relations->contains($relation)) {
            $this->relations->add($relation);
            $relation->setManga($this);
        }
        return $this;
    }

    public function removeRelation(MangaRelation $relation): self
    {
        if ($this->relations->removeElement($relation)) {
            if ($relation->getManga() === $this) {
                $relation->setManga(null);
            }
        }
        return $this;
    }

    public function getRelatedManga(): Collection
    {
        return $this->relatedManga;
    }

    public function addRelatedManga(MangaRelation $relatedManga): self
    {
        if (!$this->relatedManga->contains($relatedManga)) {
            $this->relatedManga->add($relatedManga);
            $relatedManga->setSourceManga($this);
        }
        return $this;
    }

    public function removeRelatedManga(MangaRelation $relatedManga): self
    {
        if ($this->relatedManga->removeElement($relatedManga)) {
            if ($relatedManga->getSourceManga() === $this) {
                $relatedManga->setSourceManga(null);
            }
        }
        return $this;
    }

    public function getRecommendations(): Collection
    {
        return $this->recommendations;
    }

    public function addRecommendation(MangaRecommendation $recommendation): self
    {
        if (!$this->recommendations->contains($recommendation)) {
            $this->recommendations->add($recommendation);
            $recommendation->setManga($this);
        }
        return $this;
    }

    public function removeRecommendation(MangaRecommendation $recommendation): self
    {
        if ($this->recommendations->removeElement($recommendation)) {
            if ($recommendation->getManga() === $this) {
                $recommendation->setManga(null);
            }
        }
        return $this;
    }

    public function getCustomLists(): Collection
    {
        return $this->customLists;
    }

    public function addCustomList(CustomList $customList): self
    {
        if (!$this->customLists->contains($customList)) {
            $this->customLists->add($customList);
            $customList->addManga($this);
        }
        return $this;
    }

    public function removeCustomList(CustomList $customList): self
    {
        if ($this->customLists->removeElement($customList)) {
            $customList->removeManga($this);
        }
        return $this;
    }

    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(User $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->addFollowedManga($this);
        }
        return $this;
    }

    public function removeFollower(User $follower): self
    {
        if ($this->followers->removeElement($follower)) {
            $follower->removeFollowedManga($this);
        }
        return $this;
    }

    #[Groups(['manga:read:collection', 'manga:read:item'])]
    public function getMainCoverArtFilename(): ?string
    {
        $mainCoverArt = $this->coverArts->first();
        return $mainCoverArt ? $mainCoverArt->getFileName() : null;
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
