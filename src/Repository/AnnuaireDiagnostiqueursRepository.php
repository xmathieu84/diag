<?php

namespace App\Repository;

use App\Entity\AnnuaireDiagnostiqueurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnnuaireDiagnostiqueurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnuaireDiagnostiqueurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnuaireDiagnostiqueurs[]    findAll()
 * @method AnnuaireDiagnostiqueurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnuaireDiagnostiqueursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnuaireDiagnostiqueurs::class);
    }

    // /**
    //  * @return AnnuaireDiagnostiqueurs[] Returns an array of AnnuaireDiagnostiqueurs objects
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
    public function findOneBySomeField($value): ?AnnuaireDiagnostiqueurs
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
