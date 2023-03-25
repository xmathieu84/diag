<?php

namespace App\Repository;

use App\Entity\DonneesGenerales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DonneesGenerales|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonneesGenerales|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonneesGenerales[]    findAll()
 * @method DonneesGenerales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonneesGeneralesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonneesGenerales::class);
    }

    // /**
    //  * @return DonneesGenerales[] Returns an array of DonneesGenerales objects
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
    public function findOneBySomeField($value): ?DonneesGenerales
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
