<?php

namespace App\Repository;

use App\Entity\MeteoInter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeteoInter|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeteoInter|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeteoInter[]    findAll()
 * @method MeteoInter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeteoInterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeteoInter::class);
    }

    // /**
    //  * @return MeteoInter[] Returns an array of MeteoInter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeteoInter
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
