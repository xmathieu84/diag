<?php

namespace App\Repository;

use App\Entity\ContrainteInter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContrainteInter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContrainteInter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContrainteInter[]    findAll()
 * @method ContrainteInter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContrainteInterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContrainteInter::class);
    }

    // /**
    //  * @return ContrainteInter[] Returns an array of ContrainteInter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContrainteInter
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
