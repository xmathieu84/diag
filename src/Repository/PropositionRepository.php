<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\Entreprise;
use App\Entity\Proposition;
use App\Entity\Salarie;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Proposition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proposition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proposition[]    findAll()
 * @method Proposition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proposition::class);
    }

    /**
     * @param Salarie $salarie
     * @param DateTimeInterface $date
     * @return Proposition[]
     */
    public function findBySalairieDate(Salarie $salarie, DateTimeInterface $date):array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.inter','inter')

            ->andWhere('p.salarie = :sal')

            ->andWhere('inter.statuInter = :statut')
            ->setParameter('sal', $salarie)
            ->setParameter('statut','Nouvelle demande')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByEntreprise(Entreprise $entreprise){
        return $this->createQueryBuilder('p')
            ->leftJoin('p.salarie','s')
            ->leftJoin('p.inter','i')
            ->andWhere('s.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->orderBy('i.rdvAT','DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @param Demandeur $demandeur
     * @return array
     */
    public function propositionAvecPrix($id,Demandeur $demandeur):array{
        return $this->createQueryBuilder('proposition')
            ->leftJoin('proposition.inter','inter')
            ->andWhere('inter.id = :id')
            ->andWhere('inter.intDem = :demandeur')
            ->andWhere('inter.dateWitch IS NOT NULL or inter.dateDebut IS NOT NULL')
            ->andWhere('inter.statuInter = :statut')
            ->andWhere('proposition.prix IS NOT NULL')
            ->setParameter('id',$id)
            ->setParameter('demandeur',$demandeur)
            ->setParameter('statut','Nouvelle demande')
            ->getQuery()
            ->getResult();
    }
    /**
     * @param $id
     * @param Demandeur $demandeur
     * @return array
     */
    public function propositionSansPrix($id,Demandeur $demandeur):array{
        return $this->createQueryBuilder('proposition')
            ->leftJoin('proposition.inter','inter')
            ->andWhere('inter.id = :id')
            ->andWhere('inter.intDem = :demandeur')
            ->andWhere('inter.dateWitch IS NOT NULL or inter.dateDebut IS NOT NULL')
            ->andWhere('proposition.prix IS NULL')
            ->setParameter('id',$id)
            ->setParameter('demandeur',$demandeur)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @return Proposition[]
     */
    public function findBySalarieMenu(Salarie $salarie,$date):array{
        return $this->createQueryBuilder('proposition')
            ->leftJoin('proposition.inter','inter')

            ->andWhere('proposition.salarie = :salarie')
            ->andWhere('proposition.prix IS NULL')

            ->andWhere('proposition.dateFin > :date')
            ->andWhere('inter.statuInter = :statut')
            ->setParameter('salarie',$salarie)
            ->setParameter('statut','Nouvelle demande')
            ->setParameter('date',$date)
            ->getQuery()
            ->getResult();
    }
}
