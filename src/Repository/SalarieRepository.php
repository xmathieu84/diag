<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\InterDiag;
use App\Entity\Intervention;
use App\Entity\Mission;
use App\Entity\Pack;
use App\Entity\Salarie;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method Salarie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salarie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salarie[]    findAll()
 * @method Salarie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalarieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salarie::class);
    }

    /**
     * @param Entreprise $entreprise
     * @return Salarie
     * @throws NonUniqueResultException
     */
    public function findDirirgeant(Entreprise $entreprise):Salarie{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.user','user')
            ->andWhere('user.roles LIKE :roleSalarie' )
            ->andWhere('salarie.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('roleSalarie','%"'.'ROLE_ENTREPRISE'.'"%')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Entreprise $entreprise
     * @return Salarie[]
     */
    public function findSalarie(Entreprise $entreprise):array{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.user','user')
            ->andWhere('user.roles LIKE :roleSalarie' )
            ->andWhere('salarie.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('roleSalarie','%"'.'ROLE_SALARIE'.'"%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $idListeInter
     * @param int $idTypeInter
     * @param int $idInter
     * @param DateTimeInterface $rdvInter
     * @param float $latInter
     * @param float $lonInter
     * @return Salarie[]
     */
    public function choixSalariePourIntervention(int $idListeInter, int $idTypeInter,
                                                 int $idInter, DateTimeInterface $rdvInter,
                                                 float $latInter, float $lonInter):array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.reservations', 'resa')
            ->leftJoin('s.indisponibilites', 'indispo')
            ->leftJoin('s.annulations', 'annulations')
            ->leftJoin('s.entreprise', 'entreprise')
            ->leftJoin('s.tauxHoraires', 'th')
            ->leftJoin('entreprise.etatAbonnements', 'ea')
            ->leftJoin('ea.abonnement', 'abonnement')
            ->leftJoin('annulations.intervention', 'annulInter')
            ->leftJoin('s.adresse', 'adresse')
            ->leftJoin('adresse.coordonnees', 'coordonnees')
            ->leftJoin('th.inter', 'liti')
            ->leftJoin('liti.typeInter', 'ti')
            ->leftJoin('liti.listeInter', 'li')
            ->andWhere('li.id = :idListeInter')
            ->andWhere('ti.id = :idTypeInter')
            ->andWhere(" annulInter.id != :idInter OR annulInter.id IS NULL")
            ->andWhere('indispo.debut <= :rdvInter OR indispo.debut IS NULL')
            ->andWhere('indispo.fin <= :rdvInter OR indispo.fin IS NULL')
            ->andWhere("resa.debut <= :rdvInter OR resa.debut IS NULL")
            ->andWhere('resa.depart <= :rdvInter OR resa.depart IS NULL OR (resa.debut >= :rdvInter AND resa.depart<= :rdvInter)')
            ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network'")
            ->andWhere('ea.abonne = true')
            ->andWhere('entreprise.ent_ass IS NOT NULL')
            ->andWhere('s.validation = :validation')
            ->andWhere('coordonnees.latMaxInter >= :latInter')
            ->andWhere('coordonnees.latMinInter <= :latInter')
            ->andWhere('coordonnees.lonMaxInter >= :lonInter')
            ->andWhere('coordonnees.lonMinInter <= :lonInter')
            ->setParameter('latInter', $latInter)
            ->setParameter('lonInter', $lonInter)
            ->setParameter('idListeInter', $idListeInter)
            ->setParameter('idTypeInter', $idTypeInter)
            ->setParameter('idInter', $idInter)
            ->setParameter('rdvInter', $rdvInter)
            ->setParameter('validation','valide')
            ->getQuery()
            ->getResult();
    }

    public function nombreSalarieAccueil(int $idListeInter, int $idTypeInter,

                                         float $latInter, float $lonInter){

        return $this->createQueryBuilder('s')
            ->leftJoin('s.reservations', 'resa')
            ->leftJoin('s.indisponibilites', 'indispo')
            ->leftJoin('s.annulations', 'annulations')
            ->leftJoin('s.entreprise', 'entreprise')
            ->leftJoin('s.tauxHoraires', 'th')
            ->leftJoin('entreprise.etatAbonnements', 'ea')
            ->leftJoin('ea.abonnement', 'abonnement')
            ->leftJoin('s.adresse', 'adresse')
            ->leftJoin('adresse.coordonnees', 'coordonnees')
            ->leftJoin('th.inter', 'liti')
            ->leftJoin('liti.typeInter', 'ti')
            ->leftJoin('liti.listeInter', 'li')
            ->andWhere('li.id = :idListeInter')
            ->andWhere('ti.id = :idTypeInter')

            //->andWhere("resa.debut <= :rdvInter OR resa.debut IS NULL")

            ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network'")
            ->andWhere('ea.abonne = true')
            ->andWhere('coordonnees.latMaxInter >= :latInter')
            ->andWhere('coordonnees.latMinInter <= :latInter')
            ->andWhere('coordonnees.lonMaxInter >= :lonInter')
            ->andWhere('coordonnees.lonMinInter <= :lonInter')
            ->setParameter('latInter', $latInter)
            ->setParameter('lonInter', $lonInter)
            ->setParameter('idListeInter', $idListeInter)
            ->setParameter('idTypeInter', $idTypeInter)

            ->getQuery()
            ->getResult();
    }

    /**
     * @param Intervention $intervention
     *
     * @return Salarie[]
     */
    public function findGlobal(Intervention $intervention):array{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.tauxHoraires','th')
            ->leftJoin('salarie.indisponibilites', 'indispo')
            ->leftJoin('salarie.reservations','resa')
            ->leftJoin('th.inter','inter')
            ->leftJoin('salarie.entreprise','entreprise')
            ->leftJoin('entreprise.etatAbonnements','ea')
            ->leftJoin('ea.abonnement','abonnement')
            ->leftJoin('salarie.adresse','adresse')
            ->leftJoin('adresse.coordonnees','coordonnees')
            ->andWhere('inter.typeInter = :typeInter')
            ->andWhere('inter.listeInter = :listeInter')
            ->andWhere('ea.abonne = true')
            ->andWhere('entreprise.ent_ass IS NOT NULL')
            ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network' OR abonnement.nom ='So free'")
            ->andWhere('coordonnees.latMaxInter >= :latInter')
            ->andWhere('coordonnees.latMinInter <= :latInter')
            ->andWhere('coordonnees.lonMaxInter >= :lonInter')
            ->andWhere('coordonnees.lonMinInter <= :lonInter')
            ->andWhere('salarie.validation = :validation')
            ->setParameter('typeInter',$intervention->getTypeInter())
            ->setParameter('listeInter',$intervention->getListeInter())
            ->setParameter('latInter',$intervention->getAdresse()->getCoordonnees()->getLatitude())
            ->setParameter('lonInter',$intervention->getAdresse()->getCoordonnees()->getLongitude())
            ->setParameter('validation','valide')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Intervention $intervention
     * @param DateTimeInterface|null $rdvInter
     * @return array
     */
    public function findOtdFinal(Intervention $intervention):array{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.tauxHoraires','th')
            ->leftJoin('salarie.indisponibilites', 'indispo')
            ->leftJoin('salarie.reservations','resa')
            ->leftJoin('th.inter','inter')
            ->leftJoin('salarie.entreprise','entreprise')
            ->leftJoin('entreprise.etatAbonnements','ea')
            ->leftJoin('ea.abonnement','abonnement')
            ->leftJoin('salarie.adresse','adresse')
            ->leftJoin('adresse.coordonnees','coordonnees')
            ->andWhere('inter.typeInter = :typeInter')
            ->andWhere('inter.listeInter = :listeInter')
            ->andWhere('ea.abonne = true')
            ->andWhere('entreprise.ent_ass IS NOT NULL')
            ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network' OR abonnement.nom ='So free'")
            ->andWhere('coordonnees.latMaxInter >= :latInter')
            ->andWhere('coordonnees.latMinInter <= :latInter')
            ->andWhere('coordonnees.lonMaxInter >= :lonInter')
            ->andWhere('coordonnees.lonMinInter <= :lonInter')
            ->andWhere('salarie.validation = :validation')
            ->andWhere('salarie.alphaTango IS NOT NULL')
            ->andWhere('salarie.licenceDgac IS NOT NULL')
            /*->andWhere('resa.depart <= :rdvInter OR resa.depart IS NULL OR (resa.debut >= :rdvInter AND resa.depart<= :rdvInter)')
            ->andWhere('indispo.debut <= :rdvInter OR indispo.debut IS NULL')
            ->andWhere('indispo.fin <= :rdvInter OR indispo.fin IS NULL')*/
            ->setParameter('typeInter',$intervention->getTypeInter())
            ->setParameter('listeInter',$intervention->getListeInter())
            ->setParameter('latInter',$intervention->getAdresse()->getCoordonnees()->getLatitude())
            ->setParameter('lonInter',$intervention->getAdresse()->getCoordonnees()->getLongitude())
            //->setParameter('rdvInter',$rdvInter)
            ->setParameter('validation','valide')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Intervention $intervention
     * @param float $latMin
     * @param float $latMax
     * @param float $lonMin
     * @param float $lonMax
     * @return Salarie[]
     */
    public function findOtdSup(Intervention $intervention,float $latMin,float $latMax, float $lonMin,float $lonMax):array{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.tauxHoraires','th')
            ->leftJoin('salarie.indisponibilites', 'indispo')
            ->leftJoin('salarie.reservations','resa')
            ->leftJoin('th.inter','inter')
            ->leftJoin('salarie.entreprise','entreprise')
            ->leftJoin('entreprise.etatAbonnements','ea')
            ->leftJoin('ea.abonnement','abonnement')
            ->leftJoin('salarie.adresse','adresse')
            ->leftJoin('adresse.coordonnees','coordonnees')
            ->andWhere('inter.typeInter = :typeInter')
            ->andWhere('inter.listeInter = :listeInter')
            ->andWhere('ea.abonne = true')
            ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network'")
            ->andWhere('coordonnees.latitude >= :latMin')
            ->andWhere('coordonnees.latitude <= :latMax')
            ->andWhere('coordonnees.longitude >= :lonMin')
            ->andWhere('coordonnees.longitude <= :lonMax')
            ->setParameter('typeInter',$intervention->getTypeInter())
            ->setParameter('listeInter',$intervention->getListeInter())
            ->setParameter('latMin',$latMin)
            ->setParameter('latMax',$latMax)
            ->setParameter('lonMin',$lonMin)
            ->setParameter('lonMax',$lonMax)
            ->getQuery()
            ->getResult();

    }
    public function findOtdSupFinal(Intervention $intervention,float $latMin,float $latMax, float $lonMin,float $lonMax,?DateTimeInterface $rdvInter):array{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.tauxHoraires','th')
            ->leftJoin('salarie.indisponibilites', 'indispo')
            ->leftJoin('salarie.reservations','resa')
            ->leftJoin('th.inter','inter')
            ->leftJoin('salarie.entreprise','entreprise')
            ->leftJoin('entreprise.etatAbonnements','ea')
            ->leftJoin('ea.abonnement','abonnement')
            ->leftJoin('salarie.adresse','adresse')
            ->leftJoin('adresse.coordonnees','coordonnees')
            ->andWhere('inter.typeInter = :typeInter')
            ->andWhere('inter.listeInter = :listeInter')
            ->andWhere('ea.abonne = true')
            ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network'")
            ->andWhere('coordonnees.latitude >= :latMin')
            ->andWhere('coordonnees.latitude <= :latMax')
            ->andWhere('coordonnees.longitude >= :lonMin')
            ->andWhere('coordonnees.longitude <= :lonMax')
           // ->andWhere('resa.depart <= :rdvInter OR resa.depart IS NULL OR (resa.debut >= :rdvInter AND resa.depart<= :rdvInter)')
            //->andWhere('indispo.debut <= :rdvInter OR indispo.debut IS NULL')
           // ->andWhere('indispo.fin <= :rdvInter OR indispo.fin IS NULL')
            ->setParameter('typeInter',$intervention->getTypeInter())
            ->setParameter('listeInter',$intervention->getListeInter())
            ->setParameter('latMin',$latMin)
            ->setParameter('latMax',$latMax)
            ->setParameter('lonMin',$lonMin)
            ->setParameter('lonMax',$lonMax)
            //->setParameter('rdvInter',$rdvInter)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findAllDirirgeant():array{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.user','user')
            ->andWhere('user.roles LIKE :roleSalarie' )
            ->setParameter('roleSalarie','%"'.'ROLE_ENTREPRISE'.'"%')
            ->getQuery()
            ->getResult();
    }

    public function findDirigeantByMail($email):array{
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.user','user')
            ->andWhere('user.roles LIKE :roleSalarie' )
            ->andWhere('user.email LIKE :nom')
            ->setParameter('roleSalarie','%"'.'ROLE_ENTREPRISE'.'"%')
            ->setParameter('nom','%'.$email.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Intervention $intervention
     * @return array
     */
    public function findOtdRapideGlobal(Intervention $intervention):array
    {
        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.tauxHoraires', 'th')
            ->leftJoin('salarie.indisponibilites', 'indispo')
            ->leftJoin('salarie.reservations', 'resa')
            ->leftJoin('th.inter', 'inter')
            ->leftJoin('salarie.entreprise', 'entreprise')
            ->leftJoin('entreprise.drones', 'drone')
            ->leftJoin('entreprise.etatAbonnements', 'ea')
            ->leftJoin('ea.abonnement', 'abonnement')
            ->leftJoin('salarie.adresse', 'adresse')
            ->leftJoin('adresse.coordonnees', 'coordonnees')
            ->andWhere('inter.typeInter = :typeInter')
            ->andWhere('drone.PoidDrone <= :poids')
            ->andWhere('drone.vitesse <= :vitesse')
            ->andWhere('inter.listeInter = :listeInter')
            ->andWhere('ea.abonne = true')
            ->andWhere('entreprise.ent_ass IS NOT NULL')
            ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network' OR abonnement.nom ='So free'")
            ->andWhere('coordonnees.latMaxInter >= :latInter')
            ->andWhere('coordonnees.latMinInter <= :latInter')
            ->andWhere('coordonnees.lonMaxInter >= :lonInter')
            ->andWhere('coordonnees.lonMinInter <= :lonInter')
            ->andWhere('salarie.validation = :validation')
            ->setParameter('typeInter', $intervention->getTypeInter())
            ->setParameter('listeInter', $intervention->getListeInter())
            ->setParameter('latInter', $intervention->getAdresse()->getCoordonnees()->getLatitude())
            ->setParameter('lonInter', $intervention->getAdresse()->getCoordonnees()->getLongitude())
            ->setParameter('validation', 'valide')

            ->setParameter('poids', 0.25)
            ->setParameter('vitesse',19)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Intervention $intervention
     * @param DateTimeInterface $rdvInter
     * @return array
     */
        public function findOtdRapide(Intervention $intervention,DateTimeInterface $rdvInter):array{
            return $this->createQueryBuilder('salarie')
                ->leftJoin('salarie.tauxHoraires','th')
                ->leftJoin('salarie.indisponibilites', 'indispo')
                ->leftJoin('salarie.reservations','resa')
                ->leftJoin('th.inter','inter')
                ->leftJoin('salarie.entreprise','entreprise')
                ->leftJoin('entreprise.drones','drone')
                ->leftJoin('entreprise.etatAbonnements','ea')
                ->leftJoin('ea.abonnement','abonnement')
                ->leftJoin('salarie.adresse','adresse')
                ->leftJoin('adresse.coordonnees','coordonnees')
                ->andWhere('inter.typeInter = :typeInter')
                ->andWhere('inter.listeInter = :listeInter')
                ->andWhere('ea.abonne = true')
                ->andWhere('drone.PoidDrone <= :poids')
                ->andWhere('entreprise.ent_ass IS NOT NULL')
                ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network' OR abonnement.nom ='So free'")
                ->andWhere('coordonnees.latMaxInter >= :latInter')
                ->andWhere('coordonnees.latMinInter <= :latInter')
                ->andWhere('coordonnees.lonMaxInter >= :lonInter')
                ->andWhere('coordonnees.lonMinInter <= :lonInter')
                ->andWhere('salarie.validation = :validation')
                ->andWhere('resa.depart <= :rdvInter OR resa.depart IS NULL OR (resa.debut >= :rdvInter AND resa.depart<= :rdvInter)')
                ->andWhere('indispo.debut <= :rdvInter OR indispo.debut IS NULL')
                ->andWhere('indispo.fin <= :rdvInter OR indispo.fin IS NULL')
                ->setParameter('typeInter',$intervention->getTypeInter())
                ->setParameter('listeInter',$intervention->getListeInter())
                ->setParameter('latInter',$intervention->getAdresse()->getCoordonnees()->getLatitude())
                ->setParameter('lonInter',$intervention->getAdresse()->getCoordonnees()->getLongitude())
                ->setParameter('rdvInter',$rdvInter)
                ->setParameter('validation','valide')

                ->setParameter('poids',0.25)
                ->getQuery()
                ->getResult();
        }

        public function findOtdSupRapideGlobal(Intervention $intervention,float $latMin,float $latMax, float $lonMin,float $lonMax):array{
            return $this->createQueryBuilder('salarie')
                ->leftJoin('salarie.tauxHoraires','th')
                ->leftJoin('salarie.indisponibilites', 'indispo')
                ->leftJoin('salarie.reservations','resa')
                ->leftJoin('th.inter','inter')
                ->leftJoin('salarie.entreprise','entreprise')
                ->leftJoin('entreprise.drones','drone')
                ->andWhere('drone.vitesse <= :vitesse')
                ->andWhere('drone.PoidDrone <= :poids ')
                ->leftJoin('entreprise.etatAbonnements','ea')
                ->leftJoin('ea.abonnement','abonnement')
                ->leftJoin('salarie.adresse','adresse')
                ->leftJoin('adresse.coordonnees','coordonnees')
                ->andWhere('inter.typeInter = :typeInter')
                ->andWhere('inter.listeInter = :listeInter')
                ->andWhere('ea.abonne = true')
                ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network'")
                ->andWhere('coordonnees.latitude >= :latMin')
                ->andWhere('coordonnees.latitude <= :latMax')
                ->andWhere('coordonnees.longitude >= :lonMin')
                ->andWhere('coordonnees.longitude <= :lonMax')
                ->setParameter('typeInter',$intervention->getTypeInter())
                ->setParameter('listeInter',$intervention->getListeInter())
                ->setParameter('latMin',$latMin)
                ->setParameter('latMax',$latMax)
                ->setParameter('lonMin',$lonMin)
                ->setParameter('lonMax',$lonMax)
                ->setParameter('poids',0.25)
                ->setParameter('vitesse',19)

                ->getQuery()
                ->getResult();

        }

    /**
     * @param Intervention $intervention
     * @param float $latMin
     * @param float $latMax
     * @param float $lonMin
     * @param float $lonMax
     * @param DateTimeInterface $rdvInter
     * @return array
     */
        public function findOtdSupRapide(Intervention $intervention,float $latMin,float $latMax, float $lonMin,float $lonMax,?DateTimeInterface $rdvInter):array{
            return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.tauxHoraires','th')
                ->leftJoin('salarie.indisponibilites', 'indispo')
                ->leftJoin('salarie.reservations','resa')
                ->leftJoin('th.inter','inter')
                ->leftJoin('salarie.entreprise','entreprise')
                ->leftJoin('entreprise.drones','drone')
                ->leftJoin('entreprise.etatAbonnements','ea')
                ->leftJoin('ea.abonnement','abonnement')
                ->leftJoin('salarie.adresse','adresse')
                ->leftJoin('adresse.coordonnees','coordonnees')
                ->andWhere('drone.vitesse <= :vitesse')
                ->andWhere('drone.PoidDrone <= :poids')
                ->andWhere('inter.typeInter = :typeInter')
                ->andWhere('inter.listeInter = :listeInter')
                ->andWhere('ea.abonne = true')
                ->andWhere("abonnement.nom = 'Premium network' OR abonnement.nom ='Infinite network'")
                ->andWhere('coordonnees.latitude >= :latMin')
                ->andWhere('coordonnees.latitude <= :latMax')
                ->andWhere('coordonnees.longitude >= :lonMin')
                ->andWhere('coordonnees.longitude <= :lonMax')
                //->andWhere('resa.depart <= :rdvInter OR resa.depart IS NULL OR (resa.debut >= :rdvInter AND resa.depart<= :rdvInter)')
                //->andWhere('indispo.debut <= :rdvInter OR indispo.debut IS NULL')
                //->andWhere('indispo.fin <= :rdvInter OR indispo.fin IS NULL')
                ->setParameter('typeInter',$intervention->getTypeInter())
                ->setParameter('listeInter',$intervention->getListeInter())
                ->setParameter('latMin',$latMin)
                ->setParameter('latMax',$latMax)
                ->setParameter('lonMin',$lonMin)
                ->setParameter('lonMax',$lonMax)
                //->setParameter('rdvInter',$rdvInter)
                ->setParameter('poids',0.25)
                ->setParameter('vitesse',19)
                ->getQuery()
                ->getResult();
        }

    /**
     * @return Salarie[]
     *
     */
        public function findSalarieConnecte():array{
            return $this->createQueryBuilder('salarie')
                ->leftJoin('salarie.user','user')
                ->andWhere('user.isConnect = :connect')
                ->setParameter('connect',true)
                ->getQuery()
                ->getResult();
        }

    /**
     * @param Pack $pack
     * @param Entreprise $entreprise
     * @return Salarie[]
     */
    public function findByPackEntreprise(Pack $pack,Entreprise $entreprise):array
    {
        return $this->createQueryBuilder('salarie')
            ->innerJoin('salarie.packOdis', 'packOdis')
            ->andWhere('packOdis.pack = :pack')
            ->andWhere('salarie.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('pack', $pack)
            ->getQuery()
            ->getResult();


    }

    public function findForMission(InterDiag $inter){


        return $this->createQueryBuilder('salarie')
            ->leftJoin('salarie.adresse','adresse')
            ->leftJoin('salarie.user','user')
            ->leftJoin('adresse.coordonnees','coordonnees')
            ->andWhere('coordonnees.lonMinInter <= :lon')
            ->andWhere('coordonnees.lonMaxInter >= :lon')
            ->andWhere('coordonnees.latMinInter <= :lat')
            ->andWhere('coordonnees.latMaxInter >= :lat')
            ->andWhere('user.roles LIKE :role')
            ->andWhere('salarie.isHonneur = true')
            ->setParameter('lat', $inter->getAdresse()->getCoordonnees()->getLatitude())
            ->setParameter('lon',$inter->getAdresse()->getCoordonnees()->getLongitude())
            ->setParameter('role','%"'.'ROLE_ODI'.'"%')
            ->orderBy('salarie.id')
            ->getQuery()
            ->getResult();
    }



}
