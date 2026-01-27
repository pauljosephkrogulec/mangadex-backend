<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LargeDatasetFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private FakeDataGenerator $generator;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->generator = new FakeDataGenerator();
    }

    public function load(ObjectManager $manager): void
    {
        // Generate a large dataset with 100 manga and related entities
        $this->generator->generateLargeDataset($manager, 100);
        
        $manager->flush();
    }
}
