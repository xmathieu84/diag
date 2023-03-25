<?php

namespace App\Repository;

use App\Entity\RcComplement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RcComplement|null find($id, $lockMode = null, $lockVersion = null)
 * @method RcComplement|null findOneBy(array $criteria, array $orderBy = null)
 * @method RcComplement[]    findAll()
 * @method RcComplement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RcComplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RcComplement::class);
    }

    // /**
    //  * @return RcComplement[] Returns an array of RcComplement objects
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
    public function findOneBySomeField($value): ?RcComplement
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
