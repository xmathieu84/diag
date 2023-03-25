<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\PackSupAboInsti;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

/**
 * @method PackSupAboInsti|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackSupAboInsti|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackSupAboInsti[]    findAll()
 * @method PackSupAboInsti[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackSupAboInstiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackSupAboInsti::class);
    }

    // /**
    //  * @return PackSupAboInsti[] Returns an array of PackSupAboInsti objects
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
    public function findOneBySomeField($value): ?PackSupAboInsti
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param Demandeur $demandeur
     * @param DateTimeImmutable $date
     * @return PackSupAboInsti []
     */
    public function findByDemandeur(Demandeur $demandeur, DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.aboInsti', 'a')
            ->andWhere('a.debut <= :date')
            ->andWhere('a.fin >= :date')
            ->andWhere('a.demandeur = :demandeur')
            ->setParameter('demandeur', $demandeur)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
