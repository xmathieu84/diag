<?php

namespace App\Repository;

use App\Entity\DossierOtdAo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DossierOtdAo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DossierOtdAo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DossierOtdAo[]    findAll()
 * @method DossierOtdAo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DossierOtdAoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierOtdAo::class);
    }

    // /**
    //  * @return DossierOtdAo[] Returns an array of DossierOtdAo objects
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
    public function findOneBySomeField($value): ?DossierOtdAo
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
