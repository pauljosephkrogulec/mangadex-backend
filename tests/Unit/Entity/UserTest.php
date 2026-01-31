<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testUserCreation(): void
    {
        $this->assertInstanceOf(User::class, $this->user);
        $this->assertNull($this->user->getId());
        $this->assertContains('ROLE_USER', $this->user->getRoles()); // ROLE_USER is always added
        $this->assertEquals(1, $this->user->getVersion());
    }

    public function testUsername(): void
    {
        $username = 'testuser';
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function testEmail(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testPassword(): void
    {
        $password = 'hashedpassword';
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testRoles(): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->user->setRoles($roles);
        $this->assertContains('ROLE_USER', $this->user->getRoles()); // ROLE_USER should always be included
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testDefaultRole(): void
    {
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testVersion(): void
    {
        $version = 5;
        $this->user->setVersion($version);
        $this->assertEquals($version, $this->user->getVersion());
    }

    public function testTimestamps(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01');
        $updatedAt = new \DateTimeImmutable('2023-01-02');

        $this->user->setCreatedAt($createdAt);
        $this->user->setUpdatedAt($updatedAt);

        $this->assertEquals($createdAt, $this->user->getCreatedAt());
        $this->assertEquals($updatedAt, $this->user->getUpdatedAt());
    }

    public function testUserIdentifier(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getUserIdentifier());
    }

    public function testEraseCredentials(): void
    {
        // This method should not throw any exception
        $this->user->eraseCredentials();
        $this->assertTrue(true);
    }

    public function testChaptersCollection(): void
    {
        $this->assertCount(0, $this->user->getChapters());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->user->getChapters());
    }

    public function testCoverArtsCollection(): void
    {
        $this->assertCount(0, $this->user->getCoverArts());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->user->getCoverArts());
    }

    public function testFollowedMangaCollection(): void
    {
        $this->assertCount(0, $this->user->getFollowedManga());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->user->getFollowedManga());
    }

    public function testFollowersCollection(): void
    {
        $this->assertCount(0, $this->user->getFollowers());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->user->getFollowers());
    }

    public function testFollowingCollection(): void
    {
        $this->assertCount(0, $this->user->getFollowing());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->user->getFollowing());
    }
}
