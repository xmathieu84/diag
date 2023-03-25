<?php

namespace App\Repository;

use App\Entity\DossierGeneral;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DossierGeneral|null find($id, $lockMode = null, $lockVersion = null)
 * @method DossierGeneral|null findOneBy(array $criteria, array $orderBy = null)
 * @method DossierGeneral[]    findAll()
 * @method DossierGeneral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DossierGeneralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierGeneral::class);
    }

    // /**
    //  * @return DossierGeneral[] Returns an array of DossierGeneral objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DossierGeneral
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
