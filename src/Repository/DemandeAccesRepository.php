<?php

namespace App\Repository;

use App\Entity\DemandeAcces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeAcces|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeAcces|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeAcces[]    findAll()
 * @method DemandeAcces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeAccesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeAcces::class);
    }

    // /**
    //  * @return DemandeAcces[] Returns an array of DemandeAcces objects
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
    public function findOneBySomeField($value): ?DemandeAcces
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
