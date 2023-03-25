<?php

namespace App\Repository;

use App\Entity\DevisAdmin;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DevisAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisAdmin[]    findAll()
 * @method DevisAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisAdmin::class);
    }


    /**
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return array
     */
    public function findByDateDevis(DateTimeInterface $dateDebut, DateTimeInterface $dateFin):array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.date >= :premierJour')
            ->andWhere('d.date <= :dernierJour')
            ->setParameter('premierJour', $dateDebut)
            ->setParameter('dernierJour', $dateFin)
            ->getQuery()
            ->getResult();
    }
}
