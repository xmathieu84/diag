<?php

namespace App\Repository;

use App\Entity\ContactBtp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactBtp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactBtp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactBtp[]    findAll()
 * @method ContactBtp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactBtpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactBtp::class);
    }

    // /**
    //  * @return ContactBtp[] Returns an array of ContactBtp objects
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
    public function findOneBySomeField($value): ?ContactBtp
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
