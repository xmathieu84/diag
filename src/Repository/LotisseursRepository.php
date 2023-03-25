<?php

namespace App\Repository;

use App\Entity\Lotisseurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lotisseurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lotisseurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lotisseurs[]    findAll()
 * @method Lotisseurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LotisseursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lotisseurs::class);
    }

    // /**
    //  * @return Lotisseurs[] Returns an array of Lotisseurs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lotisseurs
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
