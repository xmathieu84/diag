<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\Entreprise;
use App\Entity\Intervention;
use App\Entity\Salarie;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Intervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervention[]    findAll()
 * @method Intervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    /**
     * @param DateTimeInterface $value
     * @param DateTimeInterface $date
     * @return Intervention[]
     */
    public function findByVeilleInter(DateTimeInterface$value, DateTimeInterface $date):array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('date_diff(i.rdvAT, :val) < :date')
            ->setParameter('val', $value)
            ->setParameter('date', $date)
            ->orderBy('i.rdvAT', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $date
     * @return int|mixed|string
     */
    public function interSansProp($date,int $entreprise)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.propositions','propositions')
            ->leftJoin('propositions.salarie','salarie')
            ->leftJoin('i.contrainteInters','contrainte_inters')
            ->leftJoin('salarie.annulations','annulations')
            ->leftJoin('annulations.intervention','intervention')
            ->leftJoin('salarie.entreprise','entreprise')
            ->andWhere("i.statuInter = :statut")
            ->andWhere('contrainte_inters IS NOT NULL')
            ->andWhere('i.id <> intervention.id OR intervention.id IS NULL')
            //->andWhere('entreprise.id != :identreprise')
            ->andWhere("i.rdvAT IS NULL")
            //->setParameter('date', $date)
           //->setParameter("identreprise",$entreprise)
            ->setParameter('statut','Nouvelle demande')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTime $date
     * @return array
     */
    public function interDepasse($date): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere("i.statuInter = 'Nouvelle demande'")
            ->andWhere("i.rdvAT <= :date")
            ->setParameter('date', $date)
            ->orderBy('i.rdvAT', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTime $dateDebut
     * @param DateTime $dateFin
     * @return array
     */
    public function interDuJour($dateDebut, $dateFin): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere("i.statuInter= 'Intervention validée'")
            ->andWhere("i.rdvAT >= :dateDebut")
            ->andWhere("i.rdvAT<= :dateFin ")
            ->setParameter(":dateDebut", $dateDebut)
            ->setParameter("dateFin", $dateFin)
            ->getQuery()
            ->getResult();
    }

    public function listeInterMilitaire(
        $latMin,
        $latMax,
        $lonMin,
        $lonMax,
        $dateDebut,
        $dateFin
    ) {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.adresse', 'a')
            ->leftJoin('a.coordonnees', 'c')
            ->leftJoin('i.listeInter', 'li')
            ->andWhere('c.latitude >= :latMin')
            ->andWhere('c.latitude <= :latMax')
            ->andWhere('c.longitude >= :lonMin')
            ->andWhere('c.longitude <= :lonMax')
            ->andWhere("i.statuInter ='Intervention validée'")
            ->andWhere("li.nom = 'interventions aériennes'")
            ->andWhere("i.rdvAT >= :dateDebut")
            ->andWhere("i.rdvAT<= :dateFin")
            ->setParameter('latMin', $latMin)
            ->setParameter('latMax', $latMax)
            ->setParameter('lonMin', $lonMin)
            ->setParameter('lonMax', $lonMax)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)

            //->orderBy('i.rdvAT','ASC')
            ->getQuery()
            ->getResult();
    }

    public function interSansMap($idSalarie): array
    {
        return $this->createQueryBuilder('i')

            ->leftJoin('i.reservation', 'resa')
            ->leftJoin('resa.salarie', 'salarie')
            ->leftJoin('i.mAP', 'map')
            ->andWhere("i.statuInter = :statutInter")
            ->andWhere('map.id IS NULL')
            ->andWhere('salarie.id = :idSalarie')
            ->setParameter('statutInter', 'termine')
            ->setParameter('idSalarie', $idSalarie)
            ->getQuery()
            ->getResult();
    }

    public function listeInterInstitutionnel($latMin,$latMax,$lonMin,$lonMax,$dateDebut) {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.adresse', 'a')
            ->leftJoin('a.coordonnees', 'c')
            ->andWhere('c.latitude >= :latMin')
            ->andWhere('c.latitude <= :latMax')
            ->andWhere('c.longitude >= :lonMin')
            ->andWhere('c.longitude <= :lonMax')
            ->andWhere("i.statuInter ='Intervention validée'")
            ->andWhere("i.rdvAT >= :dateDebut")
            ->setParameter('latMin', $latMin)
            ->setParameter('latMax', $latMax)
            ->setParameter('lonMin', $lonMin)
            ->setParameter('lonMax', $lonMax)
            ->setParameter('dateDebut', $dateDebut)
            ->orderBy('i.rdvAT', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Undocumented function
     *
     * @param string $ville
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return Intervention
     */
    public function findByVille($ville, $dateDebut, $dateFin): intervention
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.adresse', 'a')
            ->andWhere('a.ville = :ville')
            ->andWhere("i.statuInter ='Intervention validée'")
            ->andWhere("i.rdvAT >= :dateDebut")
            ->andWhere("i.rdvAT<= :dateFin")
            ->setParameter('ville', $ville)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('i.rdvAT', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCodePostaux($codePostal, $dateDebut, $dateFin)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.adresse', 'a')
            ->andWhere('a.code_postal = :codePostal')
            ->andWhere("i.rdvAT >= :dateDebut")
            ->andWhere("i.rdvAT <= :dateFin")
            ->andWhere("i.statuInter ='Intervention validée'")
            ->setParameter('codePostal', $codePostal)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Intervention[]
     */
    public function findByInstitution($dateDebut,$dateFin,Entreprise $entreprise):array{
        return $this->createQueryBuilder('i')
            ->leftJoin('i.intDem','dem')
            ->leftJoin('i.reservation','r')
            ->leftJoin('r.salarie','s')
            ->andWhere('s.entreprise = :entreprise')
            ->andWhere('i.statuInter = :statut')
            ->andWhere('r.debut > :dateDebut')
            ->andWhere('r.debut < :dateFin')
            ->setParameter('dateDebut',$dateDebut)
            ->setParameter('dateFin',$dateFin)
            ->setParameter('statut','termine')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Entreprise $entreprise
     * @param DateTime $dateDebut
     * @param DateTime $dateFin
     * @return Intervention []
     */
    public function findByEntreprise(Entreprise $entreprise,DateTime $dateDebut,DateTime $dateFin):array{
        return $this->createQueryBuilder('i')
            ->leftJoin('i.reservation','r')
            ->leftJoin('r.salarie','s')
            ->andWhere('s.entreprise = :entreprise')
            ->andWhere('r.debut > :dateDebut')
            ->andWhere('r.debut < :dateFin')
            ->andWhere('i.statuInter = :statut')
            ->andWhere('i.RenoncementDelaiRetract IS NOT NULL')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('dateDebut',$dateDebut)
            ->setParameter('dateFin',$dateFin)
            ->setParameter('statut','termine')
            ->orderBy('i.rdvAT')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @return Intervention[]
     */
    public function findBySalarie(Salarie $salarie):array{
        return $this->createQueryBuilder('i')
            ->leftJoin('i.reservation','r')
            ->andWhere('r.salarie = :salarie')
            ->andWhere('i.statuInter = :statut')
            ->setParameter('salarie',$salarie)
            ->setParameter('statut','Intervention validée')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return Intervention[]
     */
    public function findBySalarieTermine(Salarie $salarie,DateTimeInterface $dateDebut,DateTimeInterface$dateFin):array{
        return $this->createQueryBuilder('i')
            ->leftJoin('i.reservation','r')
            ->andWhere('r.salarie = :salarie')
            ->andWhere('i.statuInter = :statut')
            ->andWhere('i.rdvAT >= :dateDebut')
            ->andWhere('i.rdvAT <= :dateFin')
            ->setParameter('salarie',$salarie)
            ->setParameter('statut','termine')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('i.rdvAT','DESC')
            ->getQuery()
            ->getResult();
    }

    public function missionEtat(Entreprise $entreprise,string $etat):array{
        return $this->createQueryBuilder('i')
            ->leftJoin('i.reservation','reservation')
            ->leftJoin('reservation.salarie','salarie')
            ->andWhere('salarie.entreprise = :entreprise')
            ->andWhere('i.statuInter = :statut')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('statut',$etat)
            ->getQuery()
            ->getResult();
    }

    public function missionRealisee(Entreprise $entreprise){
        return $this->createQueryBuilder('i')
            ->leftJoin('i.intDem','demandeur')
            ->leftJoin('i.reservation','reservation')
            ->leftJoin('reservation.salarie','salarie')
            ->andWhere('salarie.entreprise = :entreprise')
            ->andWhere('i.statuInter = :statut')
            ->andWhere('demandeur.user IS NOT NULL')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('statut','termine')
            ->getQuery()
            ->getResult();

    }

    public function findForInsti(string $nom,DateTime $date){
        return $this->createQueryBuilder('i')
            ->leftJoin('i.adresse','adresse')
            ->andWhere('adresse.ville LIKE :nom')
            ->andWhere('i.rdvAT > :date')
            ->andWhere('i.statuInter = :statut')
            ->setParameter('nom','%'.$nom.'%')
            ->setParameter('date',$date)
            ->setParameter('statut','Intervention validée')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Entreprise $entreprise
     * @return Intervention[]
     */
    public function findForEntreprise(Entreprise $entreprise):array{
        return $this->createQueryBuilder('intervention')
            ->leftJoin('intervention.reservation','reservation')
            ->leftJoin('intervention.rapports','rapports')
            ->leftJoin('reservation.salarie','salarie')
            ->andWhere('salarie.entreprise = :entreprise')
            ->andWhere('rapports.statu_rapport = :statut')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('statut','termine')
            ->orderBy('intervention.rdvAT','DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTime $dateDebut
     * @param DateTime $dateFin
     * @return Intervention[]
     */
    public function findForMeteo(DateTime $dateDebut,DateTime $dateFin):array{
        return $this->createQueryBuilder('intervention')
            ->andWhere('intervention.rdvAT> :date')
            ->andWhere('intervention.rdvAT< :dateFin')
            ->andWhere('intervention.statuInter = :statut')
            ->setParameter('date',$dateDebut)
            ->setParameter('dateFin',$dateFin)
            ->setParameter('statut','Intervention validée')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Entreprise $entreprise
     * @param DateTime $date
     * @return Intervention[]
     */
    public function findForMeteoInter(Entreprise $entreprise,DateTime $date):array{
        return $this->createQueryBuilder('intervention')
            ->leftJoin('intervention.reservation','reservation')
            ->leftJoin('reservation.salarie','salarie')
            ->andWhere('salarie.entreprise = :entreprise')
            ->andWhere('intervention.statuInter = :statut')
            ->andWhere('intervention.rdvAT > :date')
            ->orderBy('intervention.rdvAT','ASC')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('statut','Intervention validée')
            ->setParameter('date',$date)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTime $date
     * @param DateTime $fin
     * @return Intervention[]
     */
    public function findForSms(DateTime $date,DateTime $fin):array{
        return  $this->createQueryBuilder('intervention')
            ->leftJoin('intervention.intDem','demandeur')
            ->andWhere('intervention.rdvAT > :date')
            ->andWhere('intervention.rdvAT < :fin')
            ->andWhere('demandeur.telephon IS NOT NULL')
            ->andWhere('intervention.statuInter = :statut')
            ->setParameter('date',$date)
            ->setParameter('fin',$fin)
            ->setParameter('statut','Intervention validée')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $date
     * @return Intervention[]
     */
    public function verificationIntervention($date){
        return $this->createQueryBuilder('intervention')
            ->andWhere('intervention.statuInter = :statut')
            ->andWhere('intervention.rdvAT > :date')
            ->andWhere('intervention.rdvAT IS NOT NULL')
            ->setParameter('statut','Nouvelle demande')
            ->setParameter('date',$date)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Demandeur $demandeur
     * @return Intervention[]
     */
    public function interDemandeur(Demandeur $demandeur){
        return $this->createQueryBuilder('intervention')
            ->andWhere('intervention.statuInter = :statut')
            ->andWhere('intervention.intDem = :demandeur')
            ->andWhere('intervention.dateWitch IS NOT NULL or intervention.dateDebut IS NOT NULL')
            ->setParameter('demandeur',$demandeur)
            ->setParameter('statut','Nouvelle demande')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Demandeur $demandeur
     * @return Intervention[]
     */
    public function findInterForPub(Demandeur $demandeur):array{
        return $this->createQueryBuilder('intervention')
            ->leftJoin('intervention.travauxes','travaux')
            ->andWhere('travaux IS NOT NULL')
            ->andWhere('intervention.statuInter = :statut1 OR intervention.statuInter = :statu2')
            ->andWhere('intervention.intDem = :demandeur')
            ->setParameter('demandeur',$demandeur)
            ->setParameter('statut1','Intervention validée')
            ->setParameter('statu2','termine')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTime $date
     * @return Intervention[]
     *
     */
    public function findForContrainte(DateTime $date):array{

        return $this->createQueryBuilder('intervention')
            ->leftJoin('intervention.contrainteInters','contrainte_inters')
            ->andWhere('intervention.dateWitch > :date OR intervention.dateDebut > :date')
            ->andWhere('intervention.statuInter = :statut')
            ->setParameter("statut",'Nouvelle demande')
            ->setParameter('date',$date)
            ->getQuery()
            ->getResult();
    }



}
