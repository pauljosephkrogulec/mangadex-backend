<?php

namespace App\State;

use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasherProcessor implements ProcessorInterface
{
    private ProcessorInterface $decorated;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ProcessorInterface $decorated,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->decorated = $decorated;
        $this->passwordHasher = $passwordHasher;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof User) {
            $this->hashPassword($data);
        }

        return $this->decorated->process($data, $operation, $uriVariables, $context);
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
