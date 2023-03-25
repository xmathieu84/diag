<?php

namespace App\Repository;

use App\Entity\InterDiag;
use App\Entity\Mission;
use App\Entity\PrixOdiMission;
use App\Entity\Salarie;
use App\Entity\TailleBien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrixOdiMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrixOdiMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrixOdiMission[]    findAll()
 * @method PrixOdiMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrixOdiMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrixOdiMission::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PrixOdiMission $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(PrixOdiMission $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param TailleBien $taille
     * @param Mission $mission
     * @return PrixOdiMission[]
     */
    public function findByTailleMission(TailleBien $taille,Mission $mission):array{
        return $this->createQueryBuilder('prix_odi_mission')
            ->leftJoin('prix_odi_mission.missionOdi','missionOdi')
            ->andWhere('missionOdi.mission = :mission')
            ->andWhere('prix_odi_mission.taille = :taille')
            ->setParameter('mission',$mission)
            ->setParameter('taille',$taille)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @return PrixOdiMission[]
     */
    public function findBySalarie(Salarie $salarie):array{

        return $this->createQueryBuilder('prixOdiMission')
            ->leftJoin('prixOdiMission.missionOdi','mission_odi')
            ->andWhere('mission_odi.odi = :salarie')
            ->setParameter('salarie',$salarie)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $mission
     * @param int $idTaille
     * @return PrixOdiMission|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findForTemps(int $mission,int $idTaille,Salarie $salarie):?PrixOdiMission{
        return $this->createQueryBuilder('prixOdiMission')
            ->leftJoin('prixOdiMission.taille','taille')
            ->leftJoin('prixOdiMission.missionOdi','missionOdi')
            ->leftJoin('missionOdi.mission','mission')
            ->andWhere('mission.id = :idMission')
            ->andWhere('missionOdi.odi = :salarie')
            ->andWhere('taille.id = :idTaille')
            ->setParameter('idMission',$mission)
            ->setParameter('idTaille',$idTaille)
            ->setParameter('salarie',$salarie)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function findForInter(array $nom,InterDiag $inter):array{

        return $this->createQueryBuilder('prixOdiMission')
            ->leftJoin('prixOdiMission.missionOdi','missionOdi')
            ->leftJoin('missionOdi.odi','odi')
            ->leftJoin('odi.entreprise','entreprise')
            ->leftJoin('missionOdi.mission','mission')
            ->leftJoin('odi.adresse','adresse')
            ->leftJoin('adresse.coordonnees','coordonnees')
            ->andWhere('mission.nomSimple IN (:nom)')
            ->andWhere('odi.isHonneur = :honneur')
            ->andWhere('prixOdiMission.prix <>:prix')
            ->andWhere('prixOdiMission.prix IS NOT NULL')
            ->andWhere('prixOdiMission.isValide = :valide')
            ->andWhere('prixOdiMission.taille = :taille')
            ->andWhere('entreprise.ent_ass IS NOT NULL')
            ->andWhere('coordonnees.latMinInter <= :lat')
            ->andWhere('coordonnees.latMaxInter >= :lat')
            ->andWhere('coordonnees.lonMaxInter >= :lon')
            ->andWhere('coordonnees.lonMinInter <= :lon')
            ->groupBy('odi')
            ->setParameter('nom', $nom)
            ->setParameter('taille',$inter->getTailleBien())
            ->setParameter('honneur',true)
            ->setParameter('valide',true)
            ->setParameter('lat',$inter->getAdresse()->getCoordonnees()->getLatitude())
            ->setParameter("lon",$inter->getAdresse()->getCoordonnees()->getLongitude())
            ->setParameter('prix',0)
            ->getQuery()
            ->getResult();
    }

    public function findForInterMission(Salarie $salarie,array $mission,TailleBien $taille):array{
        return $this->createQueryBuilder('prixOdiMission')
            ->leftJoin('prixOdiMission.missionOdi','missionOdi')
            ->andWhere('missionOdi.odi = :salarie')
            ->andWhere('prixOdiMission.taille = :taille')
            ->andWhere('missionOdi.mission IN (:mission)')
            ->setParameter('salarie',$salarie)
            ->setParameter('mission',$mission)
            ->setParameter('taille',$taille)
            ->getQuery()
            ->getResult();
    }

    public function findForFacture(Salarie $odi,Mission $mission,TailleBien $tailleBien):?PrixOdiMission{
        return $this->createQueryBuilder('prixOdiMission')
            ->leftJoin('prixOdiMission.missionOdi','missionOdi')
            ->andWhere('missionOdi.mission = :mission')
            ->andWhere('prixOdiMission.taille = :taille')
            ->andWhere('missionOdi.odi = :odi')
            ->setParameters(['mission'=>$mission,"odi"=>$odi,'taille'=>$tailleBien])
            ->getQuery()
            ->getOneOrNullResult();

    }
}
