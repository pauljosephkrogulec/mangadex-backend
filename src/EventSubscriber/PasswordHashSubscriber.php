<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordHashSubscriber implements EventSubscriber
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $this->hashPassword($entity);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $this->hashPassword($entity);
    }

    private function hashPassword(User $user): void
    {
        if (empty($user->getPassword())) {
            return;
        }

        // Only hash if the password is not already hashed
        if (!$this->isPasswordHashed($user->getPassword())) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }
    }

    private function isPasswordHashed(string $password): bool
    {
        // Check if password starts with typical hash prefixes
        $hashPrefixes = [
            '$2y$', // bcrypt
            '$2a$', // bcrypt
            '$2b$', // bcrypt
            '$argon2id$', // Argon2id
            '$argon2i$',  // Argon2i
        ];

        foreach ($hashPrefixes as $prefix) {
            if (str_starts_with($password, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
