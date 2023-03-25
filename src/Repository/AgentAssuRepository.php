<?php

namespace App\Repository;

use App\Entity\AgentAssu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AgentAssu|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgentAssu|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgentAssu[]    findAll()
 * @method AgentAssu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentAssuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgentAssu::class);
    }

    // /**
    //  * @return AgentAssu[] Returns an array of AgentAssu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AgentAssu
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
