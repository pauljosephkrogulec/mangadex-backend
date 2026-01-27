<?php

namespace App\Tests\Integration\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Entity\Manga;
use App\Entity\Author;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MangaApiTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private string $jwtToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = self::getContainer()->get('security.password_hasher');
        
        // Create a test user and get JWT token
        $this->jwtToken = $this->createTestUserAndGetToken();
    }

    private function createTestUserAndGetToken(): string
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Get JWT token
        $response = static::createClient()->request('POST', '/api/login', [
            'json' => [
                'email' => 'test@example.com',
                'password' => 'password123'
            ]
        ]);

        return $response->toArray()['token'] ?? '';
    }

    public function testGetMangaCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/manga', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['@context' => '/api/contexts/Manga']);
        $this->assertJsonContains(['@type' => 'hydra:Collection']);
    }

    public function testCreateManga(): void
    {
        // Create test author and tag
        $author = new Author();
        $author->setName(['en' => 'Test Author']);
        $this->entityManager->persist($author);

        $tag = new Tag();
        $tag->setName(['en' => 'Action']);
        $tag->setTagGroup('genre');
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $response = static::createClient()->request('POST', '/api/manga', [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'title' => ['en' => 'Test Manga'],
                'description' => ['en' => 'A test manga for API testing'],
                'status' => 'ongoing',
                'contentRating' => 'safe',
                'originalLanguage' => 'ja',
                'publicationDemographic' => 'shounen',
                'year' => 2023,
                'authors' => ['/api/authors/' . $author->getId()],
                'tags' => ['/api/tags/' . $tag->getId()]
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['title' => ['en' => 'Test Manga']]);
        $this->assertJsonContains(['status' => 'ongoing']);
    }

    public function testGetMangaItem(): void
    {
        // Create a test manga first
        $manga = new Manga();
        $manga->setTitle(['en' => 'Test Manga for GET']);
        $manga->setDescription(['en' => 'Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        
        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', "/api/manga/{$manga->getId()}", [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['title' => ['en' => 'Test Manga for GET']]);
    }

    public function testUpdateManga(): void
    {
        // Create a test manga first
        $manga = new Manga();
        $manga->setTitle(['en' => 'Original Title']);
        $manga->setDescription(['en' => 'Original Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        
        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $response = static::createClient()->request('PATCH', "/api/manga/{$manga->getId()}", [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'title' => ['en' => 'Updated Title'],
                'status' => 'completed'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['title' => ['en' => 'Updated Title']]);
        $this->assertJsonContains(['status' => 'completed']);
    }

    public function testDeleteManga(): void
    {
        // Create a test manga first
        $manga = new Manga();
        $manga->setTitle(['en' => 'Manga to Delete']);
        $manga->setDescription(['en' => 'Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        
        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $mangaId = $manga->getId();

        $response = static::createClient()->request('DELETE', "/api/manga/{$mangaId}", [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->entityManager->find(Manga::class, $mangaId));
    }

    public function testSearchMangaByTitle(): void
    {
        // Create test manga with specific title
        $manga = new Manga();
        $manga->setTitle(['en' => 'Unique Search Title']);
        $manga->setDescription(['en' => 'Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        
        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/manga?title=Unique', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testFilterMangaByStatus(): void
    {
        // Create test manga with specific status
        $manga = new Manga();
        $manga->setTitle(['en' => 'Completed Manga']);
        $manga->setDescription(['en' => 'Description']);
        $manga->setStatus('completed');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        
        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/manga?status=completed', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testFilterMangaByContentRating(): void
    {
        // Create test manga with specific content rating
        $manga = new Manga();
        $manga->setTitle(['en' => 'Suggestive Manga']);
        $manga->setDescription(['en' => 'Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('suggestive');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        
        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/manga?contentRating=suggestive', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testOrderMangaByYear(): void
    {
        // Create test manga with different years
        for ($year = 2020; $year <= 2023; $year++) {
            $manga = new Manga();
            $manga->setTitle(['en' => "Manga from {$year}"]);
            $manga->setDescription(['en' => 'Description']);
            $manga->setStatus('ongoing');
            $manga->setContentRating('safe');
            $manga->setOriginalLanguage('ja');
            $manga->setYear($year);
            $manga->setState('published');
            
            $this->entityManager->persist($manga);
        }
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/manga?order[year]=desc', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testUnauthorizedAccess(): void
    {
        $response = static::createClient()->request('GET', '/api/manga');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testValidationErrors(): void
    {
        $response = static::createClient()->request('POST', '/api/manga', [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'title' => [], // Empty title should cause validation error
                'status' => 'invalid_status', // Invalid status
                'contentRating' => 'invalid_rating' // Invalid content rating
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations']);
    }
}
