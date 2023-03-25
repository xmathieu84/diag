<?php

namespace App\Repository;

use App\Entity\Ambassadeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ambassadeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ambassadeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ambassadeur[]    findAll()
 * @method Ambassadeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmbassadeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ambassadeur::class);
    }

    public function ambassadeurByHabitant(int $habitant,string $code):Ambassadeur{
        return $this->createQueryBuilder('ambassadeur')
            ->leftJoin('ambassadeur.abonnementGci','abonnementGci')
            ->andWhere('ambassadeur.codeReduc = :code')
            ->andWhere('abonnementGci.limiteB <= :habitant')
            ->andWhere('abonnementGci.limiteH >= :habitant')
            ->setParameter('habitant',$habitant)
            ->setParameter('code',$code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
