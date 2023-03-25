<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\EtatAbonnement;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method EtatAbonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatAbonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatAbonnement[]    findAll()
 * @method EtatAbonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatAbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatAbonnement::class);
    }


    /**
     * Undocumented function
     *
     * @param integer $entreprise
     * @param integer $abonnement
     * @param [type] $date
     * @return EtatAbonnement|null
     * @throws NonUniqueResultException
     */
    public function trouverEtatAbo(int $entreprise, int $abonnement, $date): ?EtatAbonnement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.entreprise = :entreprise')
            ->andWhere('e.abonnement= :abonnement')
            ->andWhere('e.datefin>:date')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('abonnement', $abonnement)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Undocumented function
     *
     * @param integer $entreprise
     * @param DateTime $date
     * @return EtatAbonnement
     * @throws NonUniqueResultException
     */
    public function trouverEtat($entreprise, $date): ?EtatAbonnement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.entreprise = :entreprise')
            ->andWhere('e.datefin>:date')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAbonnementEntreprise(Entreprise $entreprise){
        return $this->createQueryBuilder('e')
            ->andWhere('e.entreprise = :entreprise')
            ->andWhere('e.abonne= :abonne')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('abonne',true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Undocumented function
     *
     * @param Entreprise $entreprise
     * @param DateTime $date
     * @param boolean $abonne
     * @return EtatAbonnement[]
     * @throws NonUniqueResultException
     */
    public function trouverEtatAdmin(DateTime $date): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.abonnement','abonnement')
            ->andWhere('abonnement.cible = :cible')
            ->andWhere('e.datefin>:date')
            ->andWhere('e.abonne = :abonne')
            ->setParameter('date', $date)
            ->setParameter('abonne',true)
            ->setParameter('cible','otd')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $idEntreprise
     * @param int $nbreSalarie
     * @param datetime $date
     *
     * @return EtatAbonnement|null
     * @throws NonUniqueResultException
     */
    public function findByDateEntrepriseNbreOTD(int $idEntreprise, int $nbreSalarie,DateTime $date): ?EtatAbonnement
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.abonnement', 'a')
            ->leftJoin('e.entreprise', 'entreprise')
            ->andWhere('entreprise.id = :idEntreprise')
            ->andWhere('e.datefin > :dateFin')
            ->andWhere('(e.montant-a.prix)/(a.otdSup) = :nbreSalarie - a.otdMax')
            ->andWhere('e.abonne = false')
            ->setParameter('idEntreprise', $idEntreprise)
            ->setParameter('dateFin', $date)
            ->setParameter('nbreSalarie', $nbreSalarie)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEntreprise($date)
    {
        return $this->createQueryBuilder('ea')
            ->andWhere('ea.datefin > :date')
            ->setParameter('date', $date)
            ->orderBy('ea.dateDebut','DESC')
            ->getQuery()
            ->getResult();
    }

    public function findForPrelevement($dateFin){
        return $this->createQueryBuilder('etatAbonnement')
            ->andWhere('etatAbonnement.abonne  = :etat')
            ->andWhere('etatAbonnement.datefin >= :date')
            ->setParameter('etat',true)
            ->setParameter('date',$dateFin)
            ->getQuery()
            ->getResult();
    }
}
