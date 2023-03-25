<?php

namespace App\Repository;

use App\Entity\AbonnementGci;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AbonnementGci|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbonnementGci|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbonnementGci[]    findAll()
 * @method AbonnementGci[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementGciRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbonnementGci::class);
    }

    /**
     * @param $habitant
     * @param $profil
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function abonnementInsti($habitant,$profil){

        if ($habitant){
            return $this->createQueryBuilder('abonnement')
                ->andWhere('abonnement.limiteB <= :habitant')
                ->andWhere('abonnement.limiteH >= :habitant')
                ->andWhere('abonnement.profil = :profil')
                ->setParameter('habitant',$habitant)
                ->setParameter('profil',$profil)
                ->getQuery()
                ->getOneOrNullResult();
        }
        else{
            return $this->createQueryBuilder('abonnement')
                ->andWhere('abonnement.limiteB <= :habitant')
                ->andWhere('abonnement.limiteH >= :habitant')
                ->andWhere('abonnement.profil = :profil')
                ->setParameter('habitant',0)
                ->setParameter('profil',$profil)
                ->getQuery()
                ->getOneOrNullResult();
        }

    }

    /**
     * @param string $profil
     * @param $utlisateur
     * @return int|mixed|string|null
     *
     */
    public function abonnementGc($profil,$utlisateur){
        return $this->createQueryBuilder('abonnement')
            ->andWhere('abonnement.utlisateur = :utilisateur')
            ->andWhere('abonnement.profil = :profil')
            ->setParameter('profil',$profil)
            ->setParameter('utilisateur',$utlisateur)
            ->getQuery()
            ->getOneOrNullResult();
            ;
    }
}
