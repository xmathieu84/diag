<?php

namespace App\Repository;

use App\Entity\CampagneMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CampagneMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampagneMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampagneMail[]    findAll()
 * @method CampagneMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampagneMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampagneMail::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CampagneMail $entity, bool $flush = true): void
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
    public function remove(CampagneMail $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findForListe():array{
        return $this->createQueryBuilder('campagneMail')
            ->orderBy('campagneMail.date','DESC')
            ->getQuery()
            ->getResult();
    }
}
