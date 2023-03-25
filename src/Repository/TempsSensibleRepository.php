<?php

namespace App\Repository;

use App\Entity\TempsSensible;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TempsSensible|null find($id, $lockMode = null, $lockVersion = null)
 * @method TempsSensible|null findOneBy(array $criteria, array $orderBy = null)
 * @method TempsSensible[]    findAll()
 * @method TempsSensible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TempsSensibleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TempsSensible::class);
    }

    // /**
    //  * @return TempsSensible[] Returns an array of TempsSensible objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TempsSensible
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
