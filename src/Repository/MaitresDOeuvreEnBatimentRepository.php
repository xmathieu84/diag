<?php

namespace App\Repository;

use App\Entity\MaitresDOeuvreEnBatiment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MaitresDOeuvreEnBatiment|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaitresDOeuvreEnBatiment|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaitresDOeuvreEnBatiment[]    findAll()
 * @method MaitresDOeuvreEnBatiment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaitresDOeuvreEnBatimentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaitresDOeuvreEnBatiment::class);
    }

    // /**
    //  * @return MaitresDOeuvreEnBatiment[] Returns an array of MaitresDOeuvreEnBatiment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MaitresDOeuvreEnBatiment
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
