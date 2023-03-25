<?php

namespace App\Repository;

use App\Entity\ComuComMailing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ComuComMailing|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComuComMailing|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComuComMailing[]    findAll()
 * @method ComuComMailing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComuComMailingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComuComMailing::class);
    }

        public function findByNomRentre(string $nom){
        return $this->createQueryBuilder('ccm')
            ->andWhere('ccm.nom LIKE :nom')
            ->setParameter('nom','%'.$nom)
            ->getQuery()
            ->getOneOrNullResult();
        }

        public function findForInscription(string $nom):array{
        return $this->createQueryBuilder('ccm')
            ->andWhere('ccm.nom LIKE :nom')
            ->setParameter('nom','%'.$nom.'%')
            ->getQuery()
            ->getResult();

}
}
