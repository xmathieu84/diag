<?php

namespace App\Repository;

use App\Entity\Piecesgenerale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Piecesgenerale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Piecesgenerale|null findOneBy(array $criteria, array $orderBy = null)
 * @method Piecesgenerale[]    findAll()
 * @method Piecesgenerale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PiecesgeneraleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Piecesgenerale::class);
    }

    // /**
    //  * @return Piecesgenerale[] Returns an array of Piecesgenerale objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Piecesgenerale
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
