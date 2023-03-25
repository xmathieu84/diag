<?php

namespace App\Repository;

use App\Entity\ExpertAssu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpertAssu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpertAssu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpertAssu[]    findAll()
 * @method ExpertAssu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertAssuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpertAssu::class);
    }

    // /**
    //  * @return ExpertAssu[] Returns an array of ExpertAssu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExpertAssu
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
