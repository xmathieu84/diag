<?php

namespace App\Repository;

use App\Entity\InfoComplementaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InfoComplementaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoComplementaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoComplementaire[]    findAll()
 * @method InfoComplementaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoComplementaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoComplementaire::class);
    }

    // /**
    //  * @return InfoComplementaire[] Returns an array of InfoComplementaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InfoComplementaire
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
