<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findThread(User $a, User $b): array
    {
        return $this->createQueryBuilder('m')
            ->addSelect('s')
            ->addSelect('r')
            ->innerJoin('m.sender', 's')
            ->innerJoin('m.recipient', 'r')
            ->andWhere('(m.sender = :a AND m.recipient = :b) OR (m.sender = :b AND m.recipient = :a)')
            ->setParameter('a', $a)
            ->setParameter('b', $b)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestForUser(User $user, int $limit = 200): array
    {
        return $this->createQueryBuilder('m')
            ->addSelect('s')
            ->addSelect('r')
            ->innerJoin('m.sender', 's')
            ->innerJoin('m.recipient', 'r')
            ->andWhere('m.sender = :u OR m.recipient = :u')
            ->setParameter('u', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}

