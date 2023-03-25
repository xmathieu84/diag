<?php

namespace App\Repository;

use App\Entity\AppelOffre;
use App\Entity\DossierAo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DossierAo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DossierAo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DossierAo[]    findAll()
 * @method DossierAo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DossierAoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierAo::class);
    }

    // /**
    //  * @return DossierAo[] Returns an array of DossierAo objects
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
    public function findOneBySomeField($value): ?DossierAo
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
