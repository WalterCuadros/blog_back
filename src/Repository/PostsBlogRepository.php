<?php

namespace App\Repository;

use App\Entity\PostsBlog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostsBlog|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostsBlog|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostsBlog[]    findAll()
 * @method PostsBlog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsBlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostsBlog::class);
    }

    // /**
    //  * @return PostsBlog[] Returns an array of PostsBlog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PostsBlog
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
