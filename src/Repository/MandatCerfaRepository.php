<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\MandatCerfa;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MandatCerfa|null find($id, $lockMode = null, $lockVersion = null)
 * @method MandatCerfa|null findOneBy(array $criteria, array $orderBy = null)
 * @method MandatCerfa[]    findAll()
 * @method MandatCerfa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MandatCerfaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MandatCerfa::class);
    }


    /**
     * @param Entreprise $entreprise
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return MandatCerfa[]
     */
    public function nombreMandat(Entreprise $entreprise, DateTimeInterface $dateDebut, DateTimeInterface $dateFin):array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.entreprise = :entreprise')
            ->andWhere('m.date > :dateDebut')
            ->andWhere('m.date < :dateFin')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }
}
