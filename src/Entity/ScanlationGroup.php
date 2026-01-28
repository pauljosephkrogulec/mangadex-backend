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
#[ORM\Table(name: 'scanlation_groups')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['scanlation_group:read:collection']],
            paginationItemsPerPage: 10
        ),
        new Get(
            normalizationContext: ['groups' => ['scanlation_group:read:item']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['scanlation_group:write:create']],
            normalizationContext: ['groups' => ['scanlation_group:read:item']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['scanlation_group:write:update']],
            normalizationContext: ['groups' => ['scanlation_group:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['scanlation_group:write:patch']],
            normalizationContext: ['groups' => ['scanlation_group:read:item']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getLeader() == user"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'leader.id' => 'exact',
    'members.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'name'])]
class ScanlationGroup
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'chapter:read:collection', 'chapter:read:item'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'json')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private array $altNames = [];

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $website = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $ircServer = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $ircChannel = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $discord = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $contactEmail = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $twitter = null;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $mangaUpdates = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?array $focusedLanguages = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private bool $inactive = false;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private bool $locked = false;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private bool $official = false;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private bool $verified = false;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private bool $exLicensed = false;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private ?string $publishDelay = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private User $leader;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'scanlationGroups')]
    #[Groups(['scanlation_group:read:collection', 'scanlation_group:read:item', 'scanlation_group:write:create', 'scanlation_group:write:update', 'scanlation_group:write:patch'])]
    private Collection $members;

    #[ORM\ManyToMany(targetEntity: Chapter::class, mappedBy: 'groups')]
    private Collection $chapters;

    #[ORM\OneToMany(mappedBy: 'scanlationGroup', targetEntity: Report::class, cascade: ['remove'])]
    private Collection $reports;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->chapters = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAltNames(): array
    {
        return $this->altNames;
    }

    public function setAltNames(array $altNames): self
    {
        $this->altNames = $altNames;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getIrcServer(): ?string
    {
        return $this->ircServer;
    }

    public function setIrcServer(?string $ircServer): self
    {
        $this->ircServer = $ircServer;
        return $this;
    }

    public function getIrcChannel(): ?string
    {
        return $this->ircChannel;
    }

    public function setIrcChannel(?string $ircChannel): self
    {
        $this->ircChannel = $ircChannel;
        return $this;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): self
    {
        $this->discord = $discord;
        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;
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

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;
        return $this;
    }

    public function getMangaUpdates(): ?string
    {
        return $this->mangaUpdates;
    }

    public function setMangaUpdates(?string $mangaUpdates): self
    {
        $this->mangaUpdates = $mangaUpdates;
        return $this;
    }

    public function getFocusedLanguages(): ?array
    {
        return $this->focusedLanguages;
    }

    public function setFocusedLanguages(?array $focusedLanguages): self
    {
        $this->focusedLanguages = $focusedLanguages;
        return $this;
    }

    public function isInactive(): bool
    {
        return $this->inactive;
    }

    public function setInactive(bool $inactive): self
    {
        $this->inactive = $inactive;
        return $this;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;
        return $this;
    }

    public function isOfficial(): bool
    {
        return $this->official;
    }

    public function setOfficial(bool $official): self
    {
        $this->official = $official;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;
        return $this;
    }

    public function isExLicensed(): bool
    {
        return $this->exLicensed;
    }

    public function setExLicensed(bool $exLicensed): self
    {
        $this->exLicensed = $exLicensed;
        return $this;
    }

    public function getPublishDelay(): ?string
    {
        return $this->publishDelay;
    }

    public function setPublishDelay(?string $publishDelay): self
    {
        $this->publishDelay = $publishDelay;
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

    public function getLeader(): User
    {
        return $this->leader;
    }

    public function setLeader(User $leader): self
    {
        $this->leader = $leader;
        return $this;
    }

    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->addScanlationGroup($this);
        }
        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->removeElement($member)) {
            $member->removeScanlationGroup($this);
        }
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
            $chapter->addGroup($this);
        }
        return $this;
    }

    public function removeChapter(Chapter $chapter): self
    {
        if ($this->chapters->removeElement($chapter)) {
            $chapter->removeGroup($this);
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
