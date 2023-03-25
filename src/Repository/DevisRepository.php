<?php

namespace App\Repository;

use App\Entity\Devis;
use App\Entity\Entreprise;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }


    /**
     * @param Entreprise $entreprise
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return Devis[]
     */
    public function findByDateEntreprise(Entreprise $entreprise, DateTimeInterface $dateDebut, DateTimeInterface $dateFin):array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.entreprise = :entreprise')
            ->andWhere('d.date>= :dateDebut')
            ->andWhere('d.date<= :dateFin')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }
}
