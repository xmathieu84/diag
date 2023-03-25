<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Mission;
use App\Entity\PackOdiPrixTaille;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mission[]    findAll()
 * @method Mission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Mission $entity, bool $flush = true): void
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
    public function remove(Mission $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Salarie $salarie
     * @return Mission[]
     */
    public function findBySalarie(Salarie $salarie):array{
        return $this->createQueryBuilder('mission')
            ->innerJoin('mission.missionOdi','missionOdi')
            ->andWhere('missionOdi.odi = :salarie')
            ->setParameter('salarie',$salarie)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Entreprise $entreprise
     * @return array
     */
    public function findByEntreprise(Entreprise $entreprise):array{
        return $this->createQueryBuilder('mission')
            ->innerJoin('mission.missionOdi','missionOdi')
            ->innerJoin('missionOdi.odi','odi')
            ->andWhere('odi.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }

    public function findMissions(array $id):array{

        return $this->createQueryBuilder('mission')
            ->andWhere('mission.id IN (:id)')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }


    public function findMissionFromPrixPack( $pack):array{

        return $this->createQueryBuilder('mission')
            ->leftJoin('mission.packs','packs')
            ->leftJoin("packs.packOdis",'packOdis')
            ->andWhere('packOdis.id = :idPack')
            ->setParameter('idPack',$pack)
            ->getQuery()
            ->getResult();
    }

    public function findForAccueil():array{

        return $this->createQueryBuilder('mission')
            ->orderBy('mission.typeDiag','ASC')
            ->getQuery()
            ->getResult();
    }

}
