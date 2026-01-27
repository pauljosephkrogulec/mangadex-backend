<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Manga;
use App\Entity\Author;
use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

class MangaTest extends TestCase
{
    private Manga $manga;

    protected function setUp(): void
    {
        $this->manga = new Manga();
    }

    public function testMangaCreation(): void
    {
        $this->assertInstanceOf(Manga::class, $this->manga);
        $this->assertNull($this->manga->getId());
        $this->assertEquals(1, $this->manga->getVersion());
        $this->assertEquals('draft', $this->manga->getState());
        $this->assertFalse($this->manga->isLocked());
        $this->assertFalse($this->manga->isChapterNumbersResetOnNewVolume());
    }

    public function testTitle(): void
    {
        $title = ['en' => 'Test Manga'];
        $this->manga->setTitle($title);
        $this->assertEquals($title, $this->manga->getTitle());
    }

    public function testAltTitles(): void
    {
        $altTitles = ['en' => 'Alternative Title', 'ja' => '別のタイトル'];
        $this->manga->setAltTitles($altTitles);
        $this->assertEquals($altTitles, $this->manga->getAltTitles());
    }

    public function testDescription(): void
    {
        $description = ['en' => 'A test manga description'];
        $this->manga->setDescription($description);
        $this->assertEquals($description, $this->manga->getDescription());
    }

    public function testIsLocked(): void
    {
        $this->manga->setIsLocked(true);
        $this->assertTrue($this->manga->isLocked());
    }

    public function testLinks(): void
    {
        $links = ['mal' => 'https://myanimelist.net/manga/123'];
        $this->manga->setLinks($links);
        $this->assertEquals($links, $this->manga->getLinks());
    }

    public function testOfficialLinks(): void
    {
        $officialLinks = ['en' => 'https://official-site.com'];
        $this->manga->setOfficialLinks($officialLinks);
        $this->assertEquals($officialLinks, $this->manga->getOfficialLinks());
    }

    public function testOriginalLanguage(): void
    {
        $language = 'ja';
        $this->manga->setOriginalLanguage($language);
        $this->assertEquals($language, $this->manga->getOriginalLanguage());
    }

    public function testLastVolume(): void
    {
        $lastVolume = '12';
        $this->manga->setLastVolume($lastVolume);
        $this->assertEquals($lastVolume, $this->manga->getLastVolume());
    }

    public function testLastChapter(): void
    {
        $lastChapter = '150';
        $this->manga->setLastChapter($lastChapter);
        $this->assertEquals($lastChapter, $this->manga->getLastChapter());
    }

    public function testPublicationDemographic(): void
    {
        $demographic = 'shounen';
        $this->manga->setPublicationDemographic($demographic);
        $this->assertEquals($demographic, $this->manga->getPublicationDemographic());
    }

    public function testStatus(): void
    {
        $status = 'ongoing';
        $this->manga->setStatus($status);
        $this->assertEquals($status, $this->manga->getStatus());
    }

    public function testYear(): void
    {
        $year = 2023;
        $this->manga->setYear($year);
        $this->assertEquals($year, $this->manga->getYear());
    }

    public function testContentRating(): void
    {
        $contentRating = 'safe';
        $this->manga->setContentRating($contentRating);
        $this->assertEquals($contentRating, $this->manga->getContentRating());
    }

    public function testChapterNumbersResetOnNewVolume(): void
    {
        $this->manga->setChapterNumbersResetOnNewVolume(true);
        $this->assertTrue($this->manga->isChapterNumbersResetOnNewVolume());
    }

    public function testAvailableTranslatedLanguages(): void
    {
        $languages = ['en', 'es', 'fr'];
        $this->manga->setAvailableTranslatedLanguages($languages);
        $this->assertEquals($languages, $this->manga->getAvailableTranslatedLanguages());
    }

    public function testLatestUploadedChapter(): void
    {
        $chapterId = '550e8400-e29b-41d4-a716-446655440000';
        $this->manga->setLatestUploadedChapter($chapterId);
        $this->assertEquals($chapterId, $this->manga->getLatestUploadedChapter());
    }

    public function testState(): void
    {
        $state = 'published';
        $this->manga->setState($state);
        $this->assertEquals($state, $this->manga->getState());
    }

    public function testVersion(): void
    {
        $version = 5;
        $this->manga->setVersion($version);
        $this->assertEquals($version, $this->manga->getVersion());
    }

    public function testTimestamps(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01');
        $updatedAt = new \DateTimeImmutable('2023-01-02');
        
        $this->manga->setCreatedAt($createdAt);
        $this->manga->setUpdatedAt($updatedAt);
        
        $this->assertEquals($createdAt, $this->manga->getCreatedAt());
        $this->assertEquals($updatedAt, $this->manga->getUpdatedAt());
    }

    public function testAuthorsCollection(): void
    {
        $this->assertCount(0, $this->manga->getAuthors());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->manga->getAuthors());
    }

    public function testAddAuthor(): void
    {
        $author = new Author();
        $author->setName(['en' => 'Test Author']);
        
        $this->manga->addAuthor($author);
        $this->assertCount(1, $this->manga->getAuthors());
        $this->assertTrue($this->manga->getAuthors()->contains($author));
    }

    public function testRemoveAuthor(): void
    {
        $author = new Author();
        $author->setName(['en' => 'Test Author']);
        
        $this->manga->addAuthor($author);
        $this->manga->removeAuthor($author);
        $this->assertCount(0, $this->manga->getAuthors());
    }

    public function testArtistsCollection(): void
    {
        $this->assertCount(0, $this->manga->getArtists());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->manga->getArtists());
    }

    public function testAddArtist(): void
    {
        $artist = new Author();
        $artist->setName(['en' => 'Test Artist']);
        
        $this->manga->addArtist($artist);
        $this->assertCount(1, $this->manga->getArtists());
        $this->assertTrue($this->manga->getArtists()->contains($artist));
    }

    public function testTagsCollection(): void
    {
        $this->assertCount(0, $this->manga->getTags());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->manga->getTags());
    }

    public function testAddTag(): void
    {
        $tag = new Tag();
        $tag->setName(['en' => 'Test Tag']);
        $tag->setTagGroup('genre');
        
        $this->manga->addTag($tag);
        $this->assertCount(1, $this->manga->getTags());
        $this->assertTrue($this->manga->getTags()->contains($tag));
    }

    public function testChaptersCollection(): void
    {
        $this->assertCount(0, $this->manga->getChapters());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->manga->getChapters());
    }

    public function testCoverArtsCollection(): void
    {
        $this->assertCount(0, $this->manga->getCoverArts());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->manga->getCoverArts());
    }

    public function testFollowersCollection(): void
    {
        $this->assertCount(0, $this->manga->getFollowers());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->manga->getFollowers());
    }
}
