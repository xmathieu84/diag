<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\Intervention;
use App\Entity\Reservation;

use App\Entity\Salarie;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Proxies\__CG__\App\Entity\Entreprise;


/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * @param Intervention $intervention
     * @param Salarie $salarie
     * @return Reservation[]
     */
    public function findByStatuInter(Intervention $intervention,Salarie $salarie):array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.intervention = :inter')
            ->andWhere('r.salarie = :sal')
            ->setParameter('inter', $intervention)
            ->setParameter('sal', $salarie)
            ->getQuery()
            ->getResult();
    }

    /**
     * Undocumented function
     *
     *
     * @param DateTimeImmutable $dateDebut
     * @param DateTimeImmutable $dateFin
     * @param Salarie $salarie
     * @return Reservation[]
     */
    public function findResaVide(DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin,Salarie $salarie)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.salarie = :salarie')
            ->andWhere('r.debut >= :debut')
            ->andWhere('r.depart <= :dateFin')
            ->setParameter('salarie', $salarie)
            ->setParameter('debut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver reservation par satut d'intervention
     *
     * @param string $statut
     * @param Demandeur $demandeur
     * @return Reservation[]
     */
    public function findByIntervention(string $statut,Demandeur $demandeur): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.intervention', 'i')
            ->andWhere('i.intDem = :demandeur')
            ->andWhere('i.statuInter = :statut')
            ->setParameter('statut', $statut)
            ->setParameter('demandeur', $demandeur)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Demandeur $demandeur
     * @param string $statut
     * @return Reservation[]
     */
    public function findByInterStatut(Demandeur $demandeur,string $statut):array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.intervention','i')
            ->andWhere('i.intDem = :demandeur')
            ->andWhere('i.statuInter = :statut')
            ->setParameter('statut',$statut)
            ->setParameter('demandeur',$demandeur)
            ->getQuery()
            ->getResult();
    }

    public function findByEntreprise(Entreprise $entreprise){
        return $this->createQueryBuilder('reservation')
            ->leftJoin('reservation.salarie','salarie')
            ->andWhere('salarie.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @param string $statut
     * @return array
     */
     public function findInterBySalarie(Salarie $salarie,string $statut):array{

        return $this->createQueryBuilder('reservation')
            ->leftJoin('reservation.intervention','intervention')
            ->leftJoin('intervention.propositionChoisie','propositionChoisie')
            ->andWhere('propositionChoisie.salarie = :salarie')
            ->andWhere('intervention.statuInter = :statut')
            ->setParameter('salarie',$salarie)
            ->setParameter('statut',$statut)
            ->getQuery()
            ->getResult();
     }

     public function interHdd(Salarie $salarie,string $statut):array{
         return $this->createQueryBuilder('reservation')
             ->leftJoin('reservation.intervention','intervention')
             ->andWhere('reservation.salarie = :salarie')
             ->andWhere('intervention.statuInter = :statut')
             ->andWhere('intervention.type = :type')
             ->setParameter('salarie',$salarie)
             ->setParameter('statut',$statut)
             ->setParameter('type','hdd')
             ->getQuery()
             ->getResult();
     }
}
