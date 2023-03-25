<?php

namespace App\Repository;

use App\Entity\Ambassadeur;
use App\Entity\Demandeur;
use App\Entity\MailPrefecture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Demandeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demandeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demandeur[]    findAll()
 * @method Demandeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demandeur::class);
    }

    public function findInstitution()
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.agents', 'a')
            ->leftJoin('a.user','user')
            ->andWhere('a.id is not null')
            ->andWhere('user.roles LIKE :role')
            ->setParameter("role",'%"'.'ROLE_GRANDCOMPTE'.'"%')
            ->orderBy('d.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param MailPrefecture $departement
     * @param Ambassadeur $ambassadeur
     * @return Demandeur[]
     */
    public function findAmbassadeurGc(MailPrefecture $departement,Ambassadeur $ambassadeur):array{
        return $this->createQueryBuilder('demandeur')
            ->leftJoin('demandeur.adresse','adresse')
            ->andWhere('adresse.departement = :departement')
            ->andWhere('demandeur.ambassadeurGrandCompte = :ambassadeur')
            ->setParameter('ambassadeur',$ambassadeur)
            ->setParameter('departement',$departement)
            ->getQuery()
            ->getResult();

    }
    /**
     * @param MailPrefecture $departement
     * @param Ambassadeur $ambassadeur
     * @return Demandeur[]
     */
    public function findAmbassadeurInsti(MailPrefecture $departement,Ambassadeur $ambassadeur):array{
        return $this->createQueryBuilder('demandeur')
            ->leftJoin('demandeur.adresse','adresse')
            ->andWhere('adresse.departement = :departement')
            ->andWhere('demandeur.ambassadeurInsti = :ambassadeur')
            ->setParameter('ambassadeur',$ambassadeur)
            ->setParameter('departement',$departement)
            ->getQuery()
            ->getResult();

    }

    /**
     * @return Demandeur[]
     *
     */
    public function findConnect():array{
        return $this->createQueryBuilder('demandeur')
            ->leftJoin('demandeur.user','user')
            ->andWhere('user.isConnect = :connect')
            ->andWhere('user.roles LIKE :role')
            ->setParameter('connect',true)
            ->setParameter("role",'%"'.'ROLE_DEMANDEUR'.'"%')
            ->getQuery()
            ->getResult();
    }


}
