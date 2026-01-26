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
#[ORM\Table(name: 'custom_list')]
#[ApiResource(
    normalizationContext: ['groups' => ['custom_list:read']],
    denormalizationContext: ['groups' => ['custom_list:write']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'owner.id' => 'exact',
    'visibility' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'name'])]
class CustomList
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['custom_list:read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['custom_list:read', 'custom_list:write'])]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['custom_list:read', 'custom_list:write'])]
    #[Assert\NotNull]
    #[Assert\Choice(choices: ['public', 'private'])]
    private string $visibility = 'private';

    #[ORM\Column(type: 'integer')]
    #[Groups(['custom_list:read', 'custom_list:write'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['custom_list:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['custom_list:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['custom_list:read', 'custom_list:write'])]
    private User $owner;

    #[ORM\ManyToMany(targetEntity: Manga::class, inversedBy: 'customLists')]
    #[ORM\JoinTable(name: 'custom_list_manga')]
    #[Groups(['custom_list:read', 'custom_list:write'])]
    private Collection $manga;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'followedLists')]
    private Collection $followers;

    public function __construct()
    {
        $this->manga = new ArrayCollection();
        $this->followers = new ArrayCollection();
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

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;
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

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
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
            $manga->addCustomList($this);
        }
        return $this;
    }

    public function removeManga(Manga $manga): self
    {
        if ($this->manga->removeElement($manga)) {
            $manga->removeCustomList($this);
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
            $follower->addFollowedList($this);
        }
        return $this;
    }

    public function removeFollower(User $follower): self
    {
        if ($this->followers->removeElement($follower)) {
            $follower->removeFollowedList($this);
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
