<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Pack;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pack|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pack|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pack[]    findAll()
 * @method Pack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pack::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Pack $entity, bool $flush = true): void
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
    public function remove(Pack $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findPackDiagDrone(Salarie $salarie):array{
        return $this->createQueryBuilder('pack')
            ->leftJoin('pack.missions','missions')
            ->leftJoin('missions.missionOdi','missionOdi')
            ->andWhere('missionOdi.odi = :salarie')
            ->andWhere('pack.type = :nom')
            ->setParameter('salarie',$salarie)
            ->setParameter('nom','diagDrone')
            ->getQuery()
            ->getResult();
    }
    /**
     * @param Salarie $salarie
     * @return array
     *
     */
    public function findPackPerso(Salarie $salarie):array{
        return $this->createQueryBuilder('pack')
            ->leftJoin('pack.packOdis','packOdis')
            ->andWhere('packOdis.odi = :odi')
            ->andWhere('pack.type != :pack')
            ->setParameter('pack','diagDrone')
            ->setParameter('odi',$salarie)
            ->getQuery()
            ->getResult();
    }

    public function findByEntreprise(Entreprise $entreprise){
        return $this->createQueryBuilder('pack')
            ->innerJoin('pack.missions','missions')
            ->innerJoin('missions.missionOdi','missionOdi')
            ->innerJoin('missionOdi.odi','odi')
            ->andWhere('odi.entreprise = :entreprise')
            ->andWhere('pack.type = :type')
            ->setParameter('type','Diagdrone')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }

    function findOtherPack(Entreprise $entreprise):array{
        return $this->createQueryBuilder('pack')
            ->innerJoin('pack.missions','missions')
            ->innerJoin('missions.missionOdi','missionOdi')
            ->innerJoin('missionOdi.odi','odi')
            ->andWhere('odi.entreprise = :entreprise')
            ->andWhere('pack.type != :type')
            ->setParameter('type','Diagdrone')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }

    public function findBySalarie(Salarie $salarie){
        return $this->createQueryBuilder('pack')
            ->innerJoin('pack.packOdis',"packOdis")
            ->andWhere('packOdis.odi = :salarie')
            ->setParameter('salarie',$salarie)
            ->getQuery()
            ->getResult();
    }
}
