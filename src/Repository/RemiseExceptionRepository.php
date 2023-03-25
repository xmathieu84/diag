<?php

namespace App\Repository;

use App\Entity\Mission;
use App\Entity\Pack;
use App\Entity\PackOdiPrixTaille;
use App\Entity\PrixOdiMission;
use App\Entity\RemiseException;
use App\Entity\Salarie;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RemiseException|null find($id, $lockMode = null, $lockVersion = null)
 * @method RemiseException|null findOneBy(array $criteria, array $orderBy = null)
 * @method RemiseException[]    findAll()
 * @method RemiseException[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RemiseExceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RemiseException::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(RemiseException $entity, bool $flush = true): void
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
    public function remove(RemiseException $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Salarie $salarie
     * @param DateTime $date
     * @return RemiseException[]
     */
    public function findBySalarieMission(DateTime $date):array{

        return $this->createQueryBuilder('remise_exception')
            ->leftJoin('remise_exception.mission','mission')
            ->andWhere('remise_exception.fin >=:date')
            ->setParameter('date',$date)

            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @param DateTime $date
     * @return RemiseException[]
     */
    public function findBySalariePack( DateTime $date):array{
        return $this->createQueryBuilder('remise_exception')
            ->leftJoin('remise_exception.remisePack','pack')


            ->andWhere('remise_exception.fin >=:date')
            ->setParameter('date',$date)

            ->getQuery()
            ->getResult();
    }

    /**
     *
     * @param PrixOdiMission $prix
     * @return RemiseException|null
     * @throws NonUniqueResultException
     */
    public function findForInter( $dateTime,PrixOdiMission $prix):?RemiseException{

        return $this->createQueryBuilder('remise_exception')
            ->andWhere('remise_exception.mission = :prix')
            ->andWhere('remise_exception.debut < :date')
            ->andWhere('remise_exception.fin > :date')
            ->setParameter('prix',$prix)
            ->setParameter('date',$dateTime)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param DateTime $dateTime
     * @param PackOdiPrixTaille $pack
     * @return RemiseException|null
     * @throws NonUniqueResultException
     */
    public function findForPackInter(DateTime $dateTime,PackOdiPrixTaille $pack):?RemiseException{

        return $this->createQueryBuilder('remise_exception')
            ->andWhere('remise_exception.remisePack = :pack')
            ->andWhere('remise_exception.debut < :date')
            ->andWhere('remise_exception.fin > :date')
            ->setParameter('pack',$pack)
            ->setParameter('date',$dateTime)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
