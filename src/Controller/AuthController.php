<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        // Find user by email
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        // Generate JWT token
        $token = $this->jwtManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/refresh', name: 'auth_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        try {
            // Try to get user from the current token first
            $user = $this->getUser();

            if (!$user) {
                // If no user from token, check if we can get user from request data
                // This would require storing refresh tokens separately in a real implementation
                return new JsonResponse([
                    'error' => 'Token expired and no refresh mechanism available',
                    'requiresReauth' => true,
                ], 401);
            }

            // Generate new JWT token
            $newToken = $this->jwtManager->create($user);

            return new JsonResponse([
                'token' => $newToken,
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getUsername(),
                    'roles' => $user->getRoles(),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Token refresh failed',
                'requiresReauth' => true,
            ], 401);
        }
    }

    #[Route('/logout', name: 'auth_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // For JWT tokens, logout is typically handled client-side
        // but we can add server-side logic here if needed
        // such as blacklisting tokens or logging the logout event

        return new JsonResponse(['message' => 'Logged out successfully']);
    }
}
