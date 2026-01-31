<?php

namespace App\Tests\Integration\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class ApiTestCaseBase extends ApiTestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserPasswordHasherInterface $passwordHasher;
    protected string $jwtToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Don't use transaction for debugging JWT issues
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = self::getContainer()->get('security.password_hasher');

        // Create a test user and get JWT token with unique data
        $this->jwtToken = $this->createTestUserAndGetToken();
    }

    protected function tearDown(): void
    {
        // Clean up manually without transaction rollback
        if (isset($this->entityManager)) {
            $this->entityManager->clear();
        }

        parent::tearDown();
    }

    protected function createTestUserAndGetToken(): string
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

        // Create a JWT token directly without going through the API
        $jwtManager = self::getContainer()->get('lexik_jwt_authentication.jwt_manager');

        return $jwtManager->create($user);
    }

    protected function createTestAdminAndGetToken(): string
    {
        // Use unique identifiers to avoid conflicts with fixtures
        $uniqueId = uniqid('admin_', true);
        $user = new User();
        $user->setUsername($uniqueId);
        $user->setEmail($uniqueId.'@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a JWT token directly without going through the API
        $jwtManager = self::getContainer()->get('lexik_jwt_authentication.jwt_manager');

        return $jwtManager->create($user);
    }
}
