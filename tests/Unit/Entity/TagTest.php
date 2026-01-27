<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    private Tag $tag;

    protected function setUp(): void
    {
        $this->tag = new Tag();
    }

    public function testTagCreation(): void
    {
        $this->assertInstanceOf(Tag::class, $this->tag);
        $this->assertNull($this->tag->getId());
        $this->assertEquals(1, $this->tag->getVersion());
    }

    public function testName(): void
    {
        $name = ['en' => 'Action'];
        $this->tag->setName($name);
        $this->assertEquals($name, $this->tag->getName());
    }

    public function testDescription(): void
    {
        $description = ['en' => 'High energy and conflict'];
        $this->tag->setDescription($description);
        $this->assertEquals($description, $this->tag->getDescription());
    }

    public function testTagGroup(): void
    {
        $group = 'genre';
        $this->tag->setTagGroup($group);
        $this->assertEquals($group, $this->tag->getTagGroup());
    }

    public function testVersion(): void
    {
        $version = 5;
        $this->tag->setVersion($version);
        $this->assertEquals($version, $this->tag->getVersion());
    }

    public function testTimestamps(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01');
        $updatedAt = new \DateTimeImmutable('2023-01-02');
        
        $this->tag->setCreatedAt($createdAt);
        $this->tag->setUpdatedAt($updatedAt);
        
        $this->assertEquals($createdAt, $this->tag->getCreatedAt());
        $this->assertEquals($updatedAt, $this->tag->getUpdatedAt());
    }

    public function testMangaCollection(): void
    {
        $this->assertCount(0, $this->tag->getManga());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->tag->getManga());
    }

    public function testReportsCollection(): void
    {
        $this->assertCount(0, $this->tag->getReports());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->tag->getReports());
    }

    public function testValidTagGroups(): void
    {
        $validGroups = ['content', 'format', 'genre', 'theme'];
        
        foreach ($validGroups as $group) {
            $this->tag->setTagGroup($group);
            $this->assertEquals($group, $this->tag->getTagGroup());
        }
    }

    public function testMultilingualName(): void
    {
        $name = [
            'en' => 'Action',
            'ja' => 'アクション',
            'es' => 'Acción'
        ];
        $this->tag->setName($name);
        $this->assertEquals($name, $this->tag->getName());
    }

    public function testMultilingualDescription(): void
    {
        $description = [
            'en' => 'High energy and conflict',
            'ja' => '高いエネルギーと対立',
            'es' => 'Alta energía y conflicto'
        ];
        $this->tag->setDescription($description);
        $this->assertEquals($description, $this->tag->getDescription());
    }
}
