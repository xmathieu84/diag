<?php

namespace App\Repository;

use App\Entity\NoteGen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NoteGen|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoteGen|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoteGen[]    findAll()
 * @method NoteGen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteGenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoteGen::class);
    }

    // /**
    //  * @return NoteGen[] Returns an array of NoteGen objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NoteGen
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
