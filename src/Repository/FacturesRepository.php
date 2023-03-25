<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Factures;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Factures|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factures|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factures[]    findAll()
 * @method Factures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factures::class);
    }

    /**
     * @param Entreprise $entreprise
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return Factures []
     */
    public function findByDateEntreprise(Entreprise $entreprise, DateTimeInterface $dateDebut, DateTimeInterface $dateFin):array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.entreprise = :entreprise')
            ->andWhere('f.date>= :dateDebut')
            ->andWhere('f.date<= :dateFin')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }
}
