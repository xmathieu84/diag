<?php

namespace App\Repository;

use App\Entity\FactureOtd;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureOtd|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureOtd|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureOtd[]    findAll()
 * @method FactureOtd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureOtdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureOtd::class);
    }

    public function findByMonth(DateTime $debut,DateTime $fin):array{
        return $this->createQueryBuilder('facture')
            ->andWhere('facture.date >= :debut')
            ->andWhere('facture.date <= :fin')
            ->setParameter('debut',$debut)
            ->setParameter('fin',$fin)
            ->getQuery()
            ->getResult();
    }
}
