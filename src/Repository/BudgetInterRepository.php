<?php

namespace App\Repository;

use App\Entity\BudgetInter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BudgetInter|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetInter|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetInter[]    findAll()
 * @method BudgetInter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetInterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetInter::class);
    }

    // /**
    //  * @return BudgetInter[] Returns an array of BudgetInter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BudgetInter
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
