<?php

namespace App\Repository;

use App\Entity\UboDeclaration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UboDeclaration|null find($id, $lockMode = null, $lockVersion = null)
 * @method UboDeclaration|null findOneBy(array $criteria, array $orderBy = null)
 * @method UboDeclaration[]    findAll()
 * @method UboDeclaration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UboDeclarationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UboDeclaration::class);
    }

    // /**
    //  * @return UboDeclaration[] Returns an array of UboDeclaration objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UboDeclaration
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
