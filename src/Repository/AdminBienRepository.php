<?php

namespace App\Repository;

use App\Entity\AdminBien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminBien|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminBien|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminBien[]    findAll()
 * @method AdminBien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminBienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminBien::class);
    }

    // /**
    //  * @return AdminBien[] Returns an array of AdminBien objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminBien
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
