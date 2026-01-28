<?php

namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manga>
 */
class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }

    /**
     * Find manga by title search
     */
    public function searchByTitle(string $query, int $limit = 20): array
    {
        if (empty($query)) {
            return [];
        }

        return $this->createQueryBuilder('m')
            ->where('JSON_EXTRACT(m.title, :en) LIKE :search OR JSON_EXTRACT(m.title, :ja) LIKE :search')
            ->setParameter('en', '$."en"')
            ->setParameter('ja', '$."ja"')
            ->setParameter('search', '%' . $query . '%')
            ->andWhere('m.state = :state')
            ->setParameter('state', 'published')
            ->setMaxResults($limit)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get manga by content rating
     */
    public function findByContentRating(string $rating, int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.contentRating = :rating')
            ->andWhere('m.state = :state')
            ->setParameter('rating', $rating)
            ->setParameter('state', 'published')
            ->setMaxResults($limit)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get manga by status
     */
    public function findByStatus(string $status, int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.status = :status')
            ->andWhere('m.state = :state')
            ->setParameter('status', $status)
            ->setParameter('state', 'published')
            ->setMaxResults($limit)
            ->orderBy('m.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get manga by author
     */
    public function findByAuthor(int $authorId, int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.authors', 'a')
            ->where('a.id = :authorId')
            ->andWhere('m.state = :state')
            ->setParameter('authorId', $authorId)
            ->setParameter('state', 'published')
            ->setMaxResults($limit)
            ->orderBy('m.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get manga by tag
     */
    public function findByTag(int $tagId, int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.tags', 't')
            ->where('t.id = :tagId')
            ->andWhere('m.state = :state')
            ->setParameter('tagId', $tagId)
            ->setParameter('state', 'published')
            ->setMaxResults($limit)
            ->orderBy('m.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get recently updated manga
     */
    public function findRecentlyUpdated(int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.state = :state')
            ->setParameter('state', 'published')
            ->orderBy('m.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get most popular manga (by followers count)
     */
    public function findMostPopular(int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->select('m', 'COUNT(f.id) as followerCount')
            ->leftJoin('m.followers', 'f')
            ->where('m.state = :state')
            ->setParameter('state', 'published')
            ->groupBy('m.id')
            ->orderBy('followerCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
