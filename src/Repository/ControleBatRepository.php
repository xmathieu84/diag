<?php

namespace App\Repository;

use App\Entity\ControleBat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ControleBat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControleBat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControleBat[]    findAll()
 * @method ControleBat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControleBatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControleBat::class);
    }

    // /**
    //  * @return ControleBat[] Returns an array of ControleBat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ControleBat
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
