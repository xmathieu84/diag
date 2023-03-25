<?php

namespace App\Repository;

use App\Entity\Urbanistes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Urbanistes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Urbanistes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Urbanistes[]    findAll()
 * @method Urbanistes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrbanistesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Urbanistes::class);
    }

    // /**
    //  * @return Urbanistes[] Returns an array of Urbanistes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Urbanistes
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
