<?php

namespace App\Repository;

use App\Entity\MAP;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method MAP|null find($id, $lockMode = null, $lockVersion = null)
 * @method MAP|null findOneBy(array $criteria, array $orderBy = null)
 * @method MAP[]    findAll()
 * @method MAP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MAPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MAP::class);
    }


    /**
     * Undocumented function
     *
     * @param int $idSalarie
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return MAP[]
     */
    public function findMapBySalarie(int $idSalarie, DateTimeInterface $dateDebut, DateTimeInterface $dateFin): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.intervention', 'inter')
            ->leftJoin('inter.reservation', 'resa')
            ->leftJoin('resa.salarie', 'salarie')
            ->andWhere("inter.statuInter = 'termine'")
            ->andWhere('salarie.id =:idSalarie')
            ->andWhere('inter.rdvAT >= :dateDebut')
            ->andWhere('inter.rdvAT <= :dateFin')
            ->setParameter('idSalarie', $idSalarie)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }
}
