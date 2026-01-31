<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ChapterTest extends TestCase
{
    private Chapter $chapter;

    protected function setUp(): void
    {
        $this->chapter = new Chapter();
    }

    public function testChapterCreation(): void
    {
        $this->assertInstanceOf(Chapter::class, $this->chapter);
        $this->assertNull($this->chapter->getId());
        $this->assertEquals(1, $this->chapter->getVersion());
        $this->assertEquals(0, $this->chapter->getPages());
        $this->assertFalse($this->chapter->isUnavailable());
    }

    public function testTitle(): void
    {
        $title = 'Test Chapter';
        $this->chapter->setTitle($title);
        $this->assertEquals($title, $this->chapter->getTitle());
    }

    public function testVolume(): void
    {
        $volume = '1';
        $this->chapter->setVolume($volume);
        $this->assertEquals($volume, $this->chapter->getVolume());
    }

    public function testChapter(): void
    {
        $chapterNumber = '1';
        $this->chapter->setChapter($chapterNumber);
        $this->assertEquals($chapterNumber, $this->chapter->getChapter());
    }

    public function testPages(): void
    {
        $pages = 25;
        $this->chapter->setPages($pages);
        $this->assertEquals($pages, $this->chapter->getPages());
    }

    public function testTranslatedLanguage(): void
    {
        $language = 'en';
        $this->chapter->setTranslatedLanguage($language);
        $this->assertEquals($language, $this->chapter->getTranslatedLanguage());
    }

    public function testExternalUrl(): void
    {
        $url = 'https://example.com/chapter';
        $this->chapter->setExternalUrl($url);
        $this->assertEquals($url, $this->chapter->getExternalUrl());
    }

    public function testVersion(): void
    {
        $version = 5;
        $this->chapter->setVersion($version);
        $this->assertEquals($version, $this->chapter->getVersion());
    }

    public function testTimestamps(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01');
        $updatedAt = new \DateTimeImmutable('2023-01-02');

        $this->chapter->setCreatedAt($createdAt);
        $this->chapter->setUpdatedAt($updatedAt);

        $this->assertEquals($createdAt, $this->chapter->getCreatedAt());
        $this->assertEquals($updatedAt, $this->chapter->getUpdatedAt());
    }

    public function testPublishAt(): void
    {
        $publishAt = new \DateTimeImmutable('2023-01-01');
        $this->chapter->setPublishAt($publishAt);
        $this->assertEquals($publishAt, $this->chapter->getPublishAt());
    }

    public function testReadableAt(): void
    {
        $readableAt = new \DateTimeImmutable('2023-01-01');
        $this->chapter->setReadableAt($readableAt);
        $this->assertEquals($readableAt, $this->chapter->getReadableAt());
    }

    public function testIsUnavailable(): void
    {
        $this->chapter->setIsUnavailable(true);
        $this->assertTrue($this->chapter->isUnavailable());
    }

    public function testManga(): void
    {
        $manga = new Manga();
        $manga->setTitle(['en' => 'Test Manga']);

        $this->chapter->setManga($manga);
        $this->assertEquals($manga, $this->chapter->getManga());
    }

    public function testUploader(): void
    {
        $user = new User();
        $user->setUsername('testuser');

        $this->chapter->setUploader($user);
        $this->assertEquals($user, $this->chapter->getUploader());
    }

    public function testGroupsCollection(): void
    {
        $this->assertCount(0, $this->chapter->getGroups());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->chapter->getGroups());
    }

    public function testReportsCollection(): void
    {
        $this->assertCount(0, $this->chapter->getReports());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->chapter->getReports());
    }

    public function testNullTitle(): void
    {
        $this->assertNull($this->chapter->getTitle());
    }

    public function testNullVolume(): void
    {
        $this->assertNull($this->chapter->getVolume());
    }

    public function testNullChapter(): void
    {
        $this->assertNull($this->chapter->getChapter());
    }

    public function testNullExternalUrl(): void
    {
        $this->assertNull($this->chapter->getExternalUrl());
    }

    public function testNullPublishAt(): void
    {
        $this->assertNull($this->chapter->getPublishAt());
    }

    public function testNullReadableAt(): void
    {
        $this->assertNull($this->chapter->getReadableAt());
    }
}
