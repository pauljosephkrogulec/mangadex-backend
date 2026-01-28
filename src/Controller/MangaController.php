<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Entity\User;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/manga')]
class MangaController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/featured', name: 'manga_featured', methods: ['GET'])]
    public function getFeaturedManga(MangaRepository $mangaRepository): JsonResponse
    {
        $manga = $mangaRepository->findBy(['state' => 'published'], ['createdAt' => 'DESC'], 10);
        
        $data = $this->serializer->serialize($manga, 'json', [
            'groups' => ['manga:read:collection', 'cover_art:read']
        ]);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/search', name: 'manga_search', methods: ['GET'])]
    public function searchManga(Request $request, MangaRepository $mangaRepository): JsonResponse
    {
        $query = $request->query->get('q', '');
        $limit = $request->query->get('limit', 20);
        
        $manga = $mangaRepository->searchByTitle($query, $limit);
        
        $data = $this->serializer->serialize($manga, 'json', [
            'groups' => ['manga:read:collection', 'cover_art:read']
        ]);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}/publish', name: 'manga_publish', methods: ['POST'])]
    public function publishManga(Manga $manga): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($manga->getState() === 'published') {
            return new JsonResponse(['error' => 'Manga already published'], 400);
        }

        $manga->setState('published');
        $this->entityManager->flush();

        $data = $this->serializer->serialize($manga, 'json', [
            'groups' => ['manga:read:item', 'cover_art:read']
        ]);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}/draft', name: 'manga_draft', methods: ['POST'])]
    public function setDraftManga(Manga $manga): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manga->setState('draft');
        $this->entityManager->flush();

        $data = $this->serializer->serialize($manga, 'json', [
            'groups' => ['manga:read:item', 'cover_art:read']
        ]);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/statistics', name: 'manga_statistics', methods: ['GET'])]
    public function getMangaStatistics(MangaRepository $mangaRepository): JsonResponse
    {
        $stats = [
            'total' => $mangaRepository->count([]),
            'published' => $mangaRepository->count(['state' => 'published']),
            'draft' => $mangaRepository->count(['state' => 'draft']),
            'ongoing' => $mangaRepository->count(['status' => 'ongoing']),
            'completed' => $mangaRepository->count(['status' => 'completed']),
        ];

        return new JsonResponse($stats);
    }
}
