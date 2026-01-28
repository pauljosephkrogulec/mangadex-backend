<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Chapter;
use App\Entity\CoverArt;
use App\Entity\Manga;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RealMangaDexFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private HttpClientInterface $httpClient;

    public function __construct(UserPasswordHasherInterface $passwordHasher, HttpClientInterface $httpClient)
    {
        $this->passwordHasher = $passwordHasher;
        $this->httpClient = $httpClient;
    }

    public function load(ObjectManager $manager): void
    {
        // Create users
        $users = $this->createUsers($manager);
        $mangas = $this->httpClient->request('GET', 'https://api.mangadex.org/manga?limit=20&offset=0&excludedTagsMode=OR&status[]=ongoing&status[]=completed&status[]=hiatus&status[]=cancelled&publicationDemographic[]=shounen&publicationDemographic[]=shoujo&publicationDemographic[]=josei&publicationDemographic[]=seinen&publicationDemographic[]=none&contentRating[]=safe&contentRating[]=suggestive&contentRating[]=erotica&order[followedCount]=desc&includes[]=manga&includes[]=cover_art&includes[]=author&includes[]=artist&includes[]=tag&hasUnavailableChapters=0');
        $mangas = $mangas->toArray();
        foreach($mangas['data'] as $index => $manga) {
            $id = $manga['id'];
            $chapters = $this->httpClient->request('GET', "https://api.mangadex.org/chapter?manga={$id}&limit=20&includeExternalUrl=0");
            $chapters = $chapters->toArray();
            $mangas['data'][$index]['chapters'] = $chapters['data'];
            if (!empty($chapters['data'])) {
                $chapterId = $chapters['data'][0]['id'];
                $pages = $this->httpClient->request('GET', "https://api.mangadex.org/at-home/server/{$chapterId}");
                $pages = $pages->toArray(); 
                $mangas['data'][$index]['pages'] = $pages;
            }
            sleep(2);
        }
        // Create all entities
        $this->createTags($manager, $mangas['data']);
        $this->createAuthors($manager, $mangas['data'][0]['relationships']);
        $this->createArtists($manager, $mangas['data'][0]['relationships']);
        $mangaEntities = $this->createManga($manager, $mangas['data'], $users);
        $this->createChapters($manager, $mangas['data'], $users, $mangaEntities);
        $this->createCoverArts($manager, $mangas['data'], $users, $mangaEntities);
        
        $manager->flush();
    }

    private function createUsers(ObjectManager $manager): array
    {
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername("user{$i}");
            $user->setEmail("user{$i}@example.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $users[] = $user;
        }

        // Create admin user
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($admin);
        $users[] = $admin;

        return $users;
    }

    private function createManga($manager, $mangas, $users)
    {
        $mangaEntities = [];
        foreach($mangas as $manga) {
            $mangaEntity = new Manga();
            $mangaEntity->setTitle($manga['attributes']['title']);
            $mangaEntity->setDescription($manga['attributes']['description']);
            $mangaEntity->setStatus($manga['attributes']['status']);
            $mangaEntity->setPublicationDemographic($manga['attributes']['publicationDemographic']);
            $mangaEntity->setContentRating($manga['attributes']['contentRating']);
            $mangaEntity->setYear($manga['attributes']['year']);
            $mangaEntity->setOriginalLanguage($manga['attributes']['originalLanguage']);
            $mangaEntity->setAvailableTranslatedLanguages($manga['attributes']['availableTranslatedLanguages']);
            $mangaEntity->setLatestUploadedChapter($manga['attributes']['latestUploadedChapter']);
            $mangaEntity->setState('published');
            $mangaEntity->setVersion($manga['attributes']['version']);
            $mangaEntity->setCreatedAt(new \DateTimeImmutable($manga['attributes']['createdAt']));
            $mangaEntity->setUpdatedAt(new \DateTimeImmutable($manga['attributes']['updatedAt']));
            
            // Add authors
            $authorEntities = $this->createAuthors($manager, $manga['relationships']);
            foreach($authorEntities as $author) {
                $mangaEntity->addAuthor($author);
            }
            
            // Add artists
            $artistEntities = $this->createArtists($manager, $manga['relationships']);
            foreach($artistEntities as $artist) {
                $mangaEntity->addArtist($artist);
            }

            // Add tags
            $tagEntities = $this->getTags($manager, $manga['attributes']['tags']);
            foreach($tagEntities as $tag) {
                if ($tag) {
                    $mangaEntity->addTag($tag);
                }
            }
            
            $manager->persist($mangaEntity);
            $mangaEntities[$manga['id']] = $mangaEntity;
        }
        return $mangaEntities;
    }

    public function getTags($manager, $tagsData): array
    {
        $tags = [];
        foreach($tagsData as $tagData) {
            $conn = $manager->getConnection();
            $sql = "
                SELECT id
                FROM tag
                WHERE json_extract(name, '$.en') = :name
                LIMIT 1
            ";

            $id = $conn->fetchOne($sql, [
                'name' => $tagData['attributes']['name']['en'],
            ]);

            $tags[] = $id ? $manager->find(Tag::class, $id) : null;
        }
        return $tags;
    }

    private function createChapters($manager, $mangas, $users, $mangaEntities): array
    {
        $chapterEntities = [];
        foreach($mangas as $manga) {
            if (isset($manga['chapters'])) {
                foreach($manga['chapters'] as $chapterData) {
                    $chapter = new Chapter();
                    $chapter->setTitle($chapterData['attributes']['title']);
                    $chapter->setVolume($chapterData['attributes']['volume']);
                    $chapter->setChapter($chapterData['attributes']['chapter']);
                    $chapter->setPages($chapterData['attributes']['pages']);
                    $chapter->setTranslatedLanguage($chapterData['attributes']['translatedLanguage']);
                    $chapter->setExternalUrl($chapterData['attributes']['externalUrl']);
                    $chapter->setVersion($chapterData['attributes']['version']);
                    $chapter->setIsUnavailable($chapterData['attributes']['isUnavailable']);
                    
                    if ($chapterData['attributes']['publishAt']) {
                        $chapter->setPublishAt(new \DateTimeImmutable($chapterData['attributes']['publishAt']));
                    }
                    if ($chapterData['attributes']['readableAt']) {
                        $chapter->setReadableAt(new \DateTimeImmutable($chapterData['attributes']['readableAt']));
                    }
                    
                    $chapter->setCreatedAt(new \DateTimeImmutable($chapterData['attributes']['createdAt']));
                    $chapter->setUpdatedAt(new \DateTimeImmutable($chapterData['attributes']['updatedAt']));
                    
                    // Set uploader (random user for now)
                    $chapter->setUploader($users[array_rand($users)]);
                    
                    // Set manga relationship
                    $chapter->setManga($mangaEntities[$manga['id']]);
                    
                    $manager->persist($chapter);
                    $chapterEntities[] = $chapter;
                }
            }
        }
        return $chapterEntities;
    }

    private function createTags($manager, $mangas): array 
    {
        $tagEntities = [];
        $processedTags = [];
        $tagRepository = $manager->getRepository(Tag::class);
        
        foreach($mangas as $manga) {
            if (isset($manga['attributes']['tags'])) {
                foreach($manga['attributes']['tags'] as $tagData) {
                    if ($tagData['type'] === 'tag' && !isset($processedTags[$tagData['id']])) {
                        // Check if tag already exists in database
                        // Since name is stored as JSON, we need to find by comparing the array structure
                        $existingTags = $tagRepository->findAll();
                        $existingTag = null;
                        
                        foreach ($existingTags as $tag) {
                            if ($tag->getName() == $tagData['attributes']['name']) {
                                $existingTag = $tag;
                                break;
                            }
                        }
                        if ($existingTag) {
                            $tagEntities[] = $existingTag;
                            $processedTags[$tagData['id']] = $existingTag;
                        } else {
                            // Create new tag
                            $tag = new Tag();
                            $tag->setName($tagData['attributes']['name']);
                            $tag->setDescription($tagData['attributes']['description']);
                            $tag->setTagGroup($tagData['attributes']['group']);
                            $tag->setVersion($tagData['attributes']['version']);
                            $tag->setCreatedAt(new \DateTimeImmutable());
                            $tag->setUpdatedAt(new \DateTimeImmutable());
                            
                            $manager->persist($tag);
                            $tagEntities[] = $tag;
                            $processedTags[$tagData['id']] = $tag;
                        }
                    }
                }
            }
        }
        $manager->flush();
        
        return $tagEntities;
    }

    private function createAuthors($manager, $authors): array
    {
        $authorEntities = [];
        $processedAuthors = [];
        
        foreach($authors as $author) {
            if($author['type'] === 'author' && !isset($processedAuthors[$author['id']])) {
                $authorEntity = new Author();
                $authorEntity->setName(['en' => $author['attributes']['name']]);
                $authorEntity->setVersion(1);
                $authorEntity->setCreatedAt(new \DateTimeImmutable());
                $authorEntity->setUpdatedAt(new \DateTimeImmutable());
                
                if (isset($author['attributes']['twitter'])) {
                    $authorEntity->setTwitter(['en' => $author['attributes']['twitter']]);
                }
                
                $manager->persist($authorEntity);
                $authorEntities[] = $authorEntity;
                $processedAuthors[$author['id']] = $authorEntity;
            }
        }
        return $authorEntities;
    }

    private function createArtists($manager, $artists): array
    {
        $artistEntities = [];
        $processedArtists = [];
        
        foreach($artists as $artist) {
            if($artist['type'] === 'artist' && !isset($processedArtists[$artist['id']])) {
                $artistEntity = new Author();
                $artistEntity->setName(['en' => $artist['attributes']['name']]);
                $artistEntity->setVersion(1);
                $artistEntity->setCreatedAt(new \DateTimeImmutable());
                $artistEntity->setUpdatedAt(new \DateTimeImmutable());
                
                if (isset($artist['attributes']['twitter'])) {
                    $artistEntity->setTwitter(['en' => $artist['attributes']['twitter']]);
                }
                
                $manager->persist($artistEntity);
                $artistEntities[] = $artistEntity;
                $processedArtists[$artist['id']] = $artistEntity;
            }
        }
        return $artistEntities;
    }

    private function createCoverArts($manager, $mangas, $users, $mangaEntities): array
    {
        $coverArtEntities = [];
        foreach($mangas as $manga) {
            if (isset($manga['relationships'])) {
                foreach($manga['relationships'] as $relationship) {
                    if ($relationship['type'] === 'cover_art') {
                        $coverArt = new CoverArt();
                        $coverArt->setVolume($relationship['attributes']['volume'] ?? null);
                        $coverArt->setFileName($manga['id'] . '/' . $relationship['attributes']['fileName']);
                        $coverArt->setLocale($relationship['attributes']['locale'] ?? null);
                        $coverArt->setDescription($relationship['attributes']['description'] ?? null);
                        $coverArt->setVersion($relationship['attributes']['version']);
                        $coverArt->setCreatedAt(new \DateTimeImmutable($relationship['attributes']['createdAt']));
                        $coverArt->setUpdatedAt(new \DateTimeImmutable($relationship['attributes']['updatedAt']));
                        
                        // Set uploader (random user for now)
                        $coverArt->setUploader($users[array_rand($users)]);
                        
                        // Set manga relationship
                        $coverArt->setManga($mangaEntities[$manga['id']]);
                        
                        $manager->persist($coverArt);
                        $coverArtEntities[] = $coverArt;
                    }
                }
            }
        }
        return $coverArtEntities;
    }
}
