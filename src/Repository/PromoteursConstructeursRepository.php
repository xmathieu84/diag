<?php

namespace App\Repository;

use App\Entity\PromoteursConstructeurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PromoteursConstructeurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoteursConstructeurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoteursConstructeurs[]    findAll()
 * @method PromoteursConstructeurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoteursConstructeursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoteursConstructeurs::class);
    }

    // /**
    //  * @return PromoteursConstructeurs[] Returns an array of PromoteursConstructeurs objects
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
    public function findOneBySomeField($value): ?PromoteursConstructeurs
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
