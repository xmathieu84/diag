<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\InterDiag;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InterDiag|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterDiag|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterDiag[]    findAll()
 * @method InterDiag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterDiagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterDiag::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InterDiag $entity, bool $flush = true): void
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
    public function remove(InterDiag $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByUuid(string $uuid):?InterDiag{
        return $this->createQueryBuilder('interDiag')
            ->andWhere('interDiag.identifiat = :uuid')
            ->setParameter('uuid',$uuid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $identifiant
     * @param Salarie $odi
     * @param \DateTime $date
     * @return InterDiag[]
     */
    public function findInterForChoixDate(int $id,Salarie $odi, \DateTime $date):array{

        return $this->createQueryBuilder('interDiag')
            ->andWhere('interDiag.id != :id')
            ->andWhere('interDiag.odi = :odi')
            ->andWhere('interDiag.dateRdv = :date')
            ->setParameters([
                'id'=>$id,
                'odi'=>$odi,
                'date'=>$date
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Entreprise $entreprise
     * @return InterDiag[]
     *
     */
    public function findByEntreprise(Entreprise $entreprise):array{
        return $this->createQueryBuilder('interDiag')
            ->innerJoin('interDiag.odi','odi')
            ->andWhere('odi.entreprise = :entreprise')
            ->andWhere('interDiag.statut = :statut')
            ->setParameters([
                'entreprise'=>$entreprise,
                'statut'=>'Intervention validée'
            ])
            ->getQuery()
            ->getResult();
    }
}
