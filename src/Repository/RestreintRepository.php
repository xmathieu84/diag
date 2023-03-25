<?php

namespace App\Repository;

use App\Entity\Restreint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Restreint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restreint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restreint[]    findAll()
 * @method Restreint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestreintRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restreint::class);
    }

    // /**
    //  * @return Restreint[] Returns an array of Restreint objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Restreint
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
