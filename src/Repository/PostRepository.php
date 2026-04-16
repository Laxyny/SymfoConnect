<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findLatestWithAuthor(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('a')
            ->leftJoin('p.author', 'a')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findFeedForUser(User $user): array
    {
        $following = $user->getFollowing()->toArray();

        if ($following === []) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->addSelect('a')
            ->addSelect('lb')
            ->innerJoin('p.author', 'a')
            ->leftJoin('p.likedBy', 'lb')
            ->andWhere('p.author IN (:following)')
            ->setParameter('following', $following)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
