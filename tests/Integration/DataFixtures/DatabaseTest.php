<?php

namespace App\Tests\Integration\DataFixtures;

use App\DataFixtures\FakeDataGenerator;
use App\Entity\Author;
use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DatabaseTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = self::getContainer()->get('security.password_hasher');

        // Begin transaction for test isolation
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Rollback transaction to ensure test isolation
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }

        parent::tearDown();
    }

    public function testDatabaseConnection(): void
    {
        $connection = $this->entityManager->getConnection();
        $this->assertTrue($connection->isConnected());
    }

    public function testUserEntityPersistence(): void
    {
        // Use unique identifiers to avoid conflicts with fixtures
        $uniqueId = uniqid('test_', true);
        $user = new User();
        $user->setUsername($uniqueId);
        $user->setEmail($uniqueId.'@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $savedUser = $this->entityManager->find(User::class, $user->getId());
        $this->assertNotNull($savedUser);
        $this->assertEquals($uniqueId, $savedUser->getUsername());
        $this->assertEquals($uniqueId.'@example.com', $savedUser->getEmail());
        $this->assertContains('ROLE_USER', $savedUser->getRoles());
    }

    public function testMangaEntityPersistence(): void
    {
        $manga = new Manga();
        $manga->setTitle(['en' => 'Test Manga']);
        $manga->setDescription(['en' => 'Test Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');

        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $savedManga = $this->entityManager->find(Manga::class, $manga->getId());
        $this->assertNotNull($savedManga);
        $this->assertEquals(['en' => 'Test Manga'], $savedManga->getTitle());
        $this->assertEquals('ongoing', $savedManga->getStatus());
        $this->assertEquals('safe', $savedManga->getContentRating());
    }

    public function testAuthorEntityPersistence(): void
    {
        $author = new Author();
        $author->setName(['en' => 'Test Author']);
        $author->setTwitter(['en' => '@testauthor']);

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $savedAuthor = $this->entityManager->find(Author::class, $author->getId());
        $this->assertNotNull($savedAuthor);
        $this->assertEquals(['en' => 'Test Author'], $savedAuthor->getName());
        $this->assertEquals(['en' => '@testauthor'], $savedAuthor->getTwitter());
    }

    public function testTagEntityPersistence(): void
    {
        $tag = new Tag();
        $tag->setName(['en' => 'Action']);
        $tag->setDescription(['en' => 'High energy and conflict']);
        $tag->setTagGroup('genre');

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $savedTag = $this->entityManager->find(Tag::class, $tag->getId());
        $this->assertNotNull($savedTag);
        $this->assertEquals(['en' => 'Action'], $savedTag->getName());
        $this->assertEquals('genre', $savedTag->getTagGroup());
    }

    public function testMangaAuthorRelationship(): void
    {
        $author = new Author();
        $author->setName(['en' => 'Manga Author']);
        $this->entityManager->persist($author);

        $manga = new Manga();
        $manga->setTitle(['en' => 'Manga with Author']);
        $manga->setDescription(['en' => 'Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        $manga->addAuthor($author);

        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $savedManga = $this->entityManager->find(Manga::class, $manga->getId());
        $this->assertCount(1, $savedManga->getAuthors());
        $this->assertEquals($author->getId(), $savedManga->getAuthors()->first()->getId());
    }

    public function testMangaTagRelationship(): void
    {
        $tag = new Tag();
        $tag->setName(['en' => 'Romance']);
        $tag->setTagGroup('genre');
        $this->entityManager->persist($tag);

        $manga = new Manga();
        $manga->setTitle(['en' => 'Romance Manga']);
        $manga->setDescription(['en' => 'A love story']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        $manga->addTag($tag);

        $this->entityManager->persist($manga);
        $this->entityManager->flush();

        $savedManga = $this->entityManager->find(Manga::class, $manga->getId());
        $this->assertCount(1, $savedManga->getTags());
        $this->assertEquals($tag->getId(), $savedManga->getTags()->first()->getId());
    }

    public function testChapterMangaRelationship(): void
    {
        $user = new User();
        $user->setUsername('uploader');
        $user->setEmail('uploader@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);

        $manga = new Manga();
        $manga->setTitle(['en' => 'Manga with Chapters']);
        $manga->setDescription(['en' => 'Description']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');
        $this->entityManager->persist($manga);

        $chapter = new Chapter();
        $chapter->setTitle('Chapter 1');
        $chapter->setChapter('1');
        $chapter->setPages(20);
        $chapter->setTranslatedLanguage('en');
        $chapter->setManga($manga);
        $chapter->setUploader($user);

        $this->entityManager->persist($chapter);
        $this->entityManager->flush();

        $savedChapter = $this->entityManager->find(Chapter::class, $chapter->getId());
        $this->assertEquals($manga->getId(), $savedChapter->getManga()->getId());
        $this->assertEquals($user->getId(), $savedChapter->getUploader()->getId());
    }

    public function testFakeDataGenerator(): void
    {
        $generator = new FakeDataGenerator();

        // Test user generation
        $user = $generator->generateUser();
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->getUsername());
        $this->assertNotEmpty($user->getEmail());
        $this->assertContains('ROLE_USER', $user->getRoles());

        // Test manga generation
        $author = $generator->generateAuthor();
        $tag = $generator->generateTag();

        $this->entityManager->persist($author);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $manga = $generator->generateManga([$author], [$tag]);
        $this->assertInstanceOf(Manga::class, $manga);
        $this->assertNotEmpty($manga->getTitle());
        $this->assertNotEmpty($manga->getDescription());
        $this->assertNotEmpty($manga->getStatus());
        $this->assertNotEmpty($manga->getContentRating());

        // Test chapter generation
        $chapter = $generator->generateChapter($manga, $user, []);
        $this->assertInstanceOf(Chapter::class, $chapter);
        $this->assertEquals($manga->getId(), $chapter->getManga()->getId());
        $this->assertEquals($user->getId(), $chapter->getUploader()->getId());
    }

    public function testLargeDatasetGeneration(): void
    {
        $generator = new FakeDataGenerator();

        // Generate a small dataset for testing
        $generator->generateLargeDataset($this->entityManager, 5);
        $this->entityManager->flush();

        // Verify data was created
        $userCount = $this->entityManager->getRepository(User::class)->count([]);
        $mangaCount = $this->entityManager->getRepository(Manga::class)->count([]);
        $authorCount = $this->entityManager->getRepository(Author::class)->count([]);
        $tagCount = $this->entityManager->getRepository(Tag::class)->count([]);

        $this->assertGreaterThan(0, $userCount);
        $this->assertGreaterThan(0, $mangaCount);
        $this->assertGreaterThan(0, $authorCount);
        $this->assertGreaterThan(0, $tagCount);
    }

    public function testEntityTimestamps(): void
    {
        $manga = new Manga();
        $manga->setTitle(['en' => 'Timestamp Test']);
        $manga->setDescription(['en' => 'Testing timestamps']);
        $manga->setStatus('ongoing');
        $manga->setContentRating('safe');
        $manga->setOriginalLanguage('ja');
        $manga->setState('published');

        $beforeSave = new \DateTimeImmutable();
        $this->entityManager->persist($manga);
        $this->entityManager->flush();
        $afterSave = new \DateTimeImmutable();

        $savedManga = $this->entityManager->find(Manga::class, $manga->getId());

        $this->assertNotNull($savedManga->getCreatedAt());
        $this->assertNotNull($savedManga->getUpdatedAt());

        // Allow for small time differences (within 1 second)
        $createdAtDiff = abs($savedManga->getCreatedAt()->getTimestamp() - $beforeSave->getTimestamp());
        $updatedAtDiff = abs($savedManga->getUpdatedAt()->getTimestamp() - $beforeSave->getTimestamp());

        $this->assertLessThanOrEqual(1, $createdAtDiff);
        $this->assertLessThanOrEqual(1, $updatedAtDiff);
    }
}
