<?php

namespace App\Repository;

use App\Entity\OfficesEtGestionDHlm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OfficesEtGestionDHlm|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfficesEtGestionDHlm|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfficesEtGestionDHlm[]    findAll()
 * @method OfficesEtGestionDHlm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficesEtGestionDHlmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfficesEtGestionDHlm::class);
    }

    // /**
    //  * @return OfficesEtGestionDHlm[] Returns an array of OfficesEtGestionDHlm objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OfficesEtGestionDHlm
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
