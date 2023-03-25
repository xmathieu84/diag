<?php

namespace App\Repository;

use App\Entity\FamilleDiag;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FamilleDiag|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamilleDiag|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamilleDiag[]    findAll()
 * @method FamilleDiag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilleDiagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FamilleDiag::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(FamilleDiag $entity, bool $flush = true): void
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
    public function remove(FamilleDiag $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Salarie $odi
     * @return FamilleDiag[]
     */
    public function findByOdi(Salarie $odi):array{

        return $this->createQueryBuilder('famille_diag')
            ->leftJoin('famille_diag.typeDiag','type_diag')
            ->leftJoin('type_diag.mission','mission')
            ->leftJoin('mission.detailsPrix','details_prix')
            ->andWhere('details_prix.odi = :odi')
            ->andWhere('mission IS NOT NULL')
            ->setParameter('odi',$odi)
            ->getQuery()
            ->getResult();
    }
}
