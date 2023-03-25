<?php

namespace App\Repository;

use App\Entity\FactureAdmin;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureAdmin[]    findAll()
 * @method FactureAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureAdmin::class);
    }


    /**
     * @param DateTimeInterface $premierJour
     * @param DateTimeInterface $dernierJour
     * @return FactureAdmin[]
     */
    public function findByDateFacture(DateTimeInterface $premierJour, DateTimeInterface $dernierJour):array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.date >= :premierJour')
            ->andWhere('f.date <= :dernierJour')
            ->setParameter('premierJour', $premierJour)
            ->setParameter('dernierJour', $dernierJour)

            ->getQuery()
            ->getResult();
    }



}
