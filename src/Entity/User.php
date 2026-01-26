<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'username' => 'partial',
    'email' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['user:read', 'chapter:read', 'cover_art:read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 64)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['user:write'])]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 1024)]
    #[Groups(['user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 1024)]
    private string $password;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'uploader', targetEntity: Chapter::class)]
    private Collection $chapters;

    #[ORM\OneToMany(mappedBy: 'uploader', targetEntity: CoverArt::class)]
    private Collection $coverArts;

    #[ORM\ManyToMany(targetEntity: Manga::class, mappedBy: 'followers')]
    private Collection $followedManga;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'following')]
    #[ORM\JoinTable(name: 'user_follows')]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'followers')]
    private Collection $following;

    #[ORM\ManyToMany(targetEntity: CustomList::class, mappedBy: 'owner')]
    private Collection $customLists;

    #[ORM\ManyToMany(targetEntity: CustomList::class, mappedBy: 'followers')]
    private Collection $followedLists;

    #[ORM\OneToMany(mappedBy: 'leader', targetEntity: ScanlationGroup::class)]
    private Collection $ledGroups;

    #[ORM\ManyToMany(targetEntity: ScanlationGroup::class, inversedBy: 'members')]
    #[ORM\JoinTable(name: 'user_scanlation_groups')]
    private Collection $scanlationGroups;

    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'creator')]
    private Collection $reports;

    public function __construct()
    {
        $this->chapters = new ArrayCollection();
        $this->coverArts = new ArrayCollection();
        $this->followedManga = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->customLists = new ArrayCollection();
        $this->followedLists = new ArrayCollection();
        $this->ledGroups = new ArrayCollection();
        $this->scanlationGroups = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
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

    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): self
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters->add($chapter);
            $chapter->setUploader($this);
        }
        return $this;
    }

    public function removeChapter(Chapter $chapter): self
    {
        if ($this->chapters->removeElement($chapter)) {
            if ($chapter->getUploader() === $this) {
                $chapter->setUploader($this);
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
            $coverArt->setUploader($this);
        }
        return $this;
    }

    public function removeCoverArt(CoverArt $coverArt): self
    {
        if ($this->coverArts->removeElement($coverArt)) {
            if ($coverArt->getUploader() === $this) {
                $coverArt->setUploader($this);
            }
        }
        return $this;
    }

    public function getFollowedManga(): Collection
    {
        return $this->followedManga;
    }

    public function addFollowedManga(Manga $followedManga): self
    {
        if (!$this->followedManga->contains($followedManga)) {
            $this->followedManga->add($followedManga);
            $followedManga->addFollower($this);
        }
        return $this;
    }

    public function removeFollowedManga(Manga $followedManga): self
    {
        if ($this->followedManga->removeElement($followedManga)) {
            $followedManga->removeFollower($this);
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
            $follower->addFollowing($this);
        }
        return $this;
    }

    public function removeFollower(User $follower): self
    {
        if ($this->followers->removeElement($follower)) {
            $follower->removeFollowing($this);
        }
        return $this;
    }

    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(User $following): self
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
            $following->addFollower($this);
        }
        return $this;
    }

    public function removeFollowing(User $following): self
    {
        if ($this->following->removeElement($following)) {
            $following->removeFollower($this);
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
            $customList->setOwner($this);
        }
        return $this;
    }

    public function removeCustomList(CustomList $customList): self
    {
        if ($this->customLists->removeElement($customList)) {
            if ($customList->getOwner() === $this) {
                $customList->setOwner(null);
            }
        }
        return $this;
    }

    public function getFollowedLists(): Collection
    {
        return $this->followedLists;
    }

    public function addFollowedList(CustomList $followedList): self
    {
        if (!$this->followedLists->contains($followedList)) {
            $this->followedLists->add($followedList);
            $followedList->addFollower($this);
        }
        return $this;
    }

    public function removeFollowedList(CustomList $followedList): self
    {
        if ($this->followedLists->removeElement($followedList)) {
            $followedList->removeFollower($this);
        }
        return $this;
    }

    public function getLedGroups(): Collection
    {
        return $this->ledGroups;
    }

    public function addLedGroup(ScanlationGroup $ledGroup): self
    {
        if (!$this->ledGroups->contains($ledGroup)) {
            $this->ledGroups->add($ledGroup);
            $ledGroup->setLeader($this);
        }
        return $this;
    }

    public function removeLedGroup(ScanlationGroup $ledGroup): self
    {
        if ($this->ledGroups->removeElement($ledGroup)) {
            if ($ledGroup->getLeader() === $this) {
                $ledGroup->setLeader(null);
            }
        }
        return $this;
    }

    public function getScanlationGroups(): Collection
    {
        return $this->scanlationGroups;
    }

    public function addScanlationGroup(ScanlationGroup $scanlationGroup): self
    {
        if (!$this->scanlationGroups->contains($scanlationGroup)) {
            $this->scanlationGroups->add($scanlationGroup);
            $scanlationGroup->addMember($this);
        }
        return $this;
    }

    public function removeScanlationGroup(ScanlationGroup $scanlationGroup): self
    {
        if ($this->scanlationGroups->removeElement($scanlationGroup)) {
            $scanlationGroup->removeMember($this);
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
            $report->setCreator($this);
        }
        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            if ($report->getCreator() === $this) {
                $report->setCreator(null);
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

    // UserInterface required methods
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
