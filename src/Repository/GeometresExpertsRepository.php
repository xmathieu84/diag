<?php

namespace App\Repository;

use App\Entity\GeometresExperts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeometresExperts|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeometresExperts|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeometresExperts[]    findAll()
 * @method GeometresExperts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeometresExpertsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeometresExperts::class);
    }

    // /**
    //  * @return GeometresExperts[] Returns an array of GeometresExperts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GeometresExperts
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
