<?php

namespace App\Repository;


use App\Entity\MissionOdi;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MissionOdi|null find($id, $lockMode = null, $lockVersion = null)
 * @method MissionOdi|null findOneBy(array $criteria, array $orderBy = null)
 * @method MissionOdi[]    findAll()
 * @method MissionOdi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MissionOdiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissionOdi::class);
    }

    /**
     * @param MissionOdi $entity
     * @param bool $flush
     */
    public function add(MissionOdi $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param MissionOdi $entity
     * @param bool $flush
     */
    public function remove(MissionOdi $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Salarie $salarie
     * @return MissionOdi[]
     */
    public function findBySalarie(Salarie $salarie): array
    {
        return $this->createQueryBuilder('mission_odi')
            ->andWhere('mission_odi.odi = :salarie')
            ->setParameter('salarie',$salarie)
            ->getQuery()
            ->getResult();
    }



}
