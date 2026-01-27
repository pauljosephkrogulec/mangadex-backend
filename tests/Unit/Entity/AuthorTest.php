<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Author;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    private Author $author;

    protected function setUp(): void
    {
        $this->author = new Author();
    }

    public function testAuthorCreation(): void
    {
        $this->assertInstanceOf(Author::class, $this->author);
        $this->assertNull($this->author->getId());
        $this->assertEquals(1, $this->author->getVersion());
    }

    public function testName(): void
    {
        $name = ['en' => 'Test Author'];
        $this->author->setName($name);
        $this->assertEquals($name, $this->author->getName());
    }

    public function testImageUrl(): void
    {
        $imageUrl = ['en' => 'https://example.com/author.jpg'];
        $this->author->setImageUrl($imageUrl);
        $this->assertEquals($imageUrl, $this->author->getImageUrl());
    }

    public function testBiography(): void
    {
        $biography = ['en' => 'A talented author known for amazing works.'];
        $this->author->setBiography($biography);
        $this->assertEquals($biography, $this->author->getBiography());
    }

    public function testTwitter(): void
    {
        $twitter = ['en' => '@testauthor'];
        $this->author->setTwitter($twitter);
        $this->assertEquals($twitter, $this->author->getTwitter());
    }

    public function testPixiv(): void
    {
        $pixiv = ['en' => 'testauthor123'];
        $this->author->setPixiv($pixiv);
        $this->assertEquals($pixiv, $this->author->getPixiv());
    }

    public function testMelonBook(): void
    {
        $melonBook = ['en' => 'testauthor'];
        $this->author->setMelonBook($melonBook);
        $this->assertEquals($melonBook, $this->author->getMelonBook());
    }

    public function testFanBox(): void
    {
        $fanBox = ['en' => 'testauthor.fanbox'];
        $this->author->setFanBox($fanBox);
        $this->assertEquals($fanBox, $this->author->getFanBox());
    }

    public function testBooth(): void
    {
        $booth = ['en' => 'testauthor.booth'];
        $this->author->setBooth($booth);
        $this->assertEquals($booth, $this->author->getBooth());
    }

    public function testNicoVideo(): void
    {
        $nicoVideo = ['en' => 'testauthor.nico'];
        $this->author->setNicoVideo($nicoVideo);
        $this->assertEquals($nicoVideo, $this->author->getNicoVideo());
    }

    public function testSkeb(): void
    {
        $skeb = ['en' => 'testauthor.skeb'];
        $this->author->setSkeb($skeb);
        $this->assertEquals($skeb, $this->author->getSkeb());
    }

    public function testFantia(): void
    {
        $fantia = ['en' => 'testauthor.fantia'];
        $this->author->setFantia($fantia);
        $this->assertEquals($fantia, $this->author->getFantia());
    }

    public function testTumblr(): void
    {
        $tumblr = ['en' => 'testauthor.tumblr'];
        $this->author->setTumblr($tumblr);
        $this->assertEquals($tumblr, $this->author->getTumblr());
    }

    public function testYoutube(): void
    {
        $youtube = ['en' => 'testauthor.youtube'];
        $this->author->setYoutube($youtube);
        $this->assertEquals($youtube, $this->author->getYoutube());
    }

    public function testWeibo(): void
    {
        $weibo = ['en' => 'testauthor.weibo'];
        $this->author->setWeibo($weibo);
        $this->assertEquals($weibo, $this->author->getWeibo());
    }

    public function testNaver(): void
    {
        $naver = ['en' => 'testauthor.naver'];
        $this->author->setNaver($naver);
        $this->assertEquals($naver, $this->author->getNaver());
    }

    public function testWebsite(): void
    {
        $website = ['en' => 'https://testauthor.com'];
        $this->author->setWebsite($website);
        $this->assertEquals($website, $this->author->getWebsite());
    }

    public function testVersion(): void
    {
        $version = 5;
        $this->author->setVersion($version);
        $this->assertEquals($version, $this->author->getVersion());
    }

    public function testTimestamps(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01');
        $updatedAt = new \DateTimeImmutable('2023-01-02');
        
        $this->author->setCreatedAt($createdAt);
        $this->author->setUpdatedAt($updatedAt);
        
        $this->assertEquals($createdAt, $this->author->getCreatedAt());
        $this->assertEquals($updatedAt, $this->author->getUpdatedAt());
    }

    public function testMangaAsAuthorCollection(): void
    {
        $this->assertCount(0, $this->author->getMangaAsAuthor());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->author->getMangaAsAuthor());
    }

    public function testMangaAsArtistCollection(): void
    {
        $this->assertCount(0, $this->author->getMangaAsArtist());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->author->getMangaAsArtist());
    }

    public function testNullImageUrl(): void
    {
        $this->assertNull($this->author->getImageUrl());
    }

    public function testNullBiography(): void
    {
        $this->assertNull($this->author->getBiography());
    }

    public function testNullSocialMedia(): void
    {
        $this->assertNull($this->author->getTwitter());
        $this->assertNull($this->author->getPixiv());
        $this->assertNull($this->author->getMelonBook());
        $this->assertNull($this->author->getFanBox());
        $this->assertNull($this->author->getBooth());
        $this->assertNull($this->author->getNicoVideo());
        $this->assertNull($this->author->getSkeb());
        $this->assertNull($this->author->getFantia());
        $this->assertNull($this->author->getTumblr());
        $this->assertNull($this->author->getYoutube());
        $this->assertNull($this->author->getWeibo());
        $this->assertNull($this->author->getNaver());
        $this->assertNull($this->author->getWebsite());
    }

    public function testMultilingualName(): void
    {
        $name = [
            'en' => 'John Doe',
            'ja' => 'ジョン・ドウ',
            'es' => 'Juan Pérez'
        ];
        $this->author->setName($name);
        $this->assertEquals($name, $this->author->getName());
    }

    public function testMultilingualBiography(): void
    {
        $biography = [
            'en' => 'An accomplished author',
            'ja' => ' accomplishedな著者',
            'es' => 'Un autor consumado'
        ];
        $this->author->setBiography($biography);
        $this->assertEquals($biography, $this->author->getBiography());
    }
}
