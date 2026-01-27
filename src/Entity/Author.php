<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attributes\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'author')]
#[ApiResource(
    normalizationContext: ['groups' => ['author:read']],
    denormalizationContext: ['groups' => ['author:write']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'imageUrl' => 'partial',
    'biography' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'name'])]
class Author
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator')]
    #[Groups(['author:read', 'manga:read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['author:read', 'author:write'])]
    #[Assert\NotNull]
    private array $name = [];

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $imageUrl = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $biography = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $twitter = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $pixiv = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $melonBook = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $fanBox = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $booth = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $nicoVideo = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $skeb = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $fantia = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $tumblr = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $youtube = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $weibo = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $naver = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['author:read', 'author:write'])]
    private ?array $website = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['author:read', 'author:write'])]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['author:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['author:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToMany(targetEntity: Manga::class, mappedBy: 'authors')]
    private Collection $mangaAsAuthor;

    #[ORM\ManyToMany(targetEntity: Manga::class, mappedBy: 'artists')]
    private Collection $mangaAsArtist;

    public function __construct()
    {
        $this->mangaAsAuthor = new ArrayCollection();
        $this->mangaAsArtist = new ArrayCollection();
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

    public function getImageUrl(): ?array
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?array $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getBiography(): ?array
    {
        return $this->biography;
    }

    public function setBiography(?array $biography): self
    {
        $this->biography = $biography;
        return $this;
    }

    public function getTwitter(): ?array
    {
        return $this->twitter;
    }

    public function setTwitter(?array $twitter): self
    {
        $this->twitter = $twitter;
        return $this;
    }

    public function getPixiv(): ?array
    {
        return $this->pixiv;
    }

    public function setPixiv(?array $pixiv): self
    {
        $this->pixiv = $pixiv;
        return $this;
    }

    public function getMelonBook(): ?array
    {
        return $this->melonBook;
    }

    public function setMelonBook(?array $melonBook): self
    {
        $this->melonBook = $melonBook;
        return $this;
    }

    public function getFanBox(): ?array
    {
        return $this->fanBox;
    }

    public function setFanBox(?array $fanBox): self
    {
        $this->fanBox = $fanBox;
        return $this;
    }

    public function getBooth(): ?array
    {
        return $this->booth;
    }

    public function setBooth(?array $booth): self
    {
        $this->booth = $booth;
        return $this;
    }

    public function getNicoVideo(): ?array
    {
        return $this->nicoVideo;
    }

    public function setNicoVideo(?array $nicoVideo): self
    {
        $this->nicoVideo = $nicoVideo;
        return $this;
    }

    public function getSkeb(): ?array
    {
        return $this->skeb;
    }

    public function setSkeb(?array $skeb): self
    {
        $this->skeb = $skeb;
        return $this;
    }

    public function getFantia(): ?array
    {
        return $this->fantia;
    }

    public function setFantia(?array $fantia): self
    {
        $this->fantia = $fantia;
        return $this;
    }

    public function getTumblr(): ?array
    {
        return $this->tumblr;
    }

    public function setTumblr(?array $tumblr): self
    {
        $this->tumblr = $tumblr;
        return $this;
    }

    public function getYoutube(): ?array
    {
        return $this->youtube;
    }

    public function setYoutube(?array $youtube): self
    {
        $this->youtube = $youtube;
        return $this;
    }

    public function getWeibo(): ?array
    {
        return $this->weibo;
    }

    public function setWeibo(?array $weibo): self
    {
        $this->weibo = $weibo;
        return $this;
    }

    public function getNaver(): ?array
    {
        return $this->naver;
    }

    public function setNaver(?array $naver): self
    {
        $this->naver = $naver;
        return $this;
    }

    public function getWebsite(): ?array
    {
        return $this->website;
    }

    public function setWebsite(?array $website): self
    {
        $this->website = $website;
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

    public function getMangaAsAuthor(): Collection
    {
        return $this->mangaAsAuthor;
    }

    public function addMangaAsAuthor(Manga $mangaAsAuthor): self
    {
        if (!$this->mangaAsAuthor->contains($mangaAsAuthor)) {
            $this->mangaAsAuthor->add($mangaAsAuthor);
            $mangaAsAuthor->addAuthor($this);
        }
        return $this;
    }

    public function removeMangaAsAuthor(Manga $mangaAsAuthor): self
    {
        if ($this->mangaAsAuthor->removeElement($mangaAsAuthor)) {
            $mangaAsAuthor->removeAuthor($this);
        }
        return $this;
    }

    public function getMangaAsArtist(): Collection
    {
        return $this->mangaAsArtist;
    }

    public function addMangaAsArtist(Manga $mangaAsArtist): self
    {
        if (!$this->mangaAsArtist->contains($mangaAsArtist)) {
            $this->mangaAsArtist->add($mangaAsArtist);
            $mangaAsArtist->addArtist($this);
        }
        return $this;
    }

    public function removeMangaAsArtist(Manga $mangaAsArtist): self
    {
        if ($this->mangaAsArtist->removeElement($mangaAsArtist)) {
            $mangaAsArtist->removeArtist($this);
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
