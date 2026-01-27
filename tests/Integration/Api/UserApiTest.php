<?php

namespace App\Tests\Integration\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserApiTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private string $jwtToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = self::getContainer()->get('security.password_hasher');
        
        // Create an admin user and get JWT token
        $this->jwtToken = $this->createAdminUserAndGetToken();
    }

    private function createAdminUserAndGetToken(): string
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'admin123'));
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Get JWT token
        $response = static::createClient()->request('POST', '/api/login', [
            'json' => [
                'email' => 'admin@example.com',
                'password' => 'admin123'
            ]
        ]);

        return $response->toArray()['token'] ?? '';
    }

    public function testGetUserCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/users', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['@context' => '/api/contexts/User']);
        $this->assertJsonContains(['@type' => 'hydra:Collection']);
    }

    public function testCreateUser(): void
    {
        $response = static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'username' => 'newuser',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'roles' => ['ROLE_USER']
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['username' => 'newuser']);
        $this->assertJsonContains(['email' => 'newuser@example.com']);
        $this->assertJsonContains(['roles' => ['ROLE_USER']]);
    }

    public function testGetUserItem(): void
    {
        // Create a test user first
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', "/api/users/{$user->getId()}", [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['username' => 'testuser']);
        $this->assertJsonContains(['email' => 'test@example.com']);
    }

    public function testUpdateUser(): void
    {
        // Create a test user first
        $user = new User();
        $user->setUsername('updateme');
        $user->setEmail('update@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = static::createClient()->request('PATCH', "/api/users/{$user->getId()}", [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => [
                'username' => 'updateduser',
                'roles' => ['ROLE_USER', 'ROLE_ADMIN']
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['username' => 'updateduser']);
        $this->assertJsonContains(['roles' => ['ROLE_USER', 'ROLE_ADMIN']]);
    }

    public function testDeleteUser(): void
    {
        // Create a test user first
        $user = new User();
        $user->setUsername('deleteme');
        $user->setEmail('delete@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();

        $response = static::createClient()->request('DELETE', "/api/users/{$userId}", [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->entityManager->find(User::class, $userId));
    }

    public function testSearchUserByUsername(): void
    {
        // Create test user with specific username
        $user = new User();
        $user->setUsername('uniqueusername');
        $user->setEmail('unique@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/users?username=unique', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testSearchUserByEmail(): void
    {
        // Create test user with specific email
        $user = new User();
        $user->setUsername('emailtest');
        $user->setEmail('uniquemail@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/users?email=uniquemail', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testOrderUsersByUsername(): void
    {
        // Create multiple test users
        $usernames = ['alice', 'bob', 'charlie'];
        foreach ($usernames as $username) {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail("{$username}@example.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles(['ROLE_USER']);
            
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/users?order[username]=asc', [
            'headers' => ['Authorization' => "Bearer {$this->jwtToken}"]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testUnauthorizedAccess(): void
    {
        $response = static::createClient()->request('GET', '/api/users');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testValidationErrors(): void
    {
        $response = static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'username' => '', // Empty username should cause validation error
                'email' => 'invalid-email', // Invalid email format
                'password' => '123', // Password too short
                'roles' => ['INVALID_ROLE'] // Invalid role
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations']);
    }

    public function testDuplicateUsername(): void
    {
        // Create first user
        $user1 = new User();
        $user1->setUsername('duplicate');
        $user1->setEmail('first@example.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password123'));
        $user1->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user1);
        $this->entityManager->flush();

        // Try to create second user with same username
        $response = static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'username' => 'duplicate', // Same username
                'email' => 'second@example.com',
                'password' => 'password123',
                'roles' => ['ROLE_USER']
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations']);
    }

    public function testDuplicateEmail(): void
    {
        // Create first user
        $user1 = new User();
        $user1->setUsername('firstuser');
        $user1->setEmail('duplicate@example.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password123'));
        $user1->setRoles(['ROLE_USER']);
        
        $this->entityManager->persist($user1);
        $this->entityManager->flush();

        // Try to create second user with same email
        $response = static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => "Bearer {$this->jwtToken}",
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                'username' => 'seconduser',
                'email' => 'duplicate@example.com', // Same email
                'password' => 'password123',
                'roles' => ['ROLE_USER']
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations']);
    }
}
