<?php

namespace App\Repository;

use App\Entity\Journaliste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Journaliste|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journaliste|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journaliste[]    findAll()
 * @method Journaliste[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalisteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Journaliste::class);
    }

    // /**
    //  * @return Journaliste[] Returns an array of Journaliste objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Journaliste
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
