<?php

namespace App\Repository;

use App\Entity\AboTotalInsti;
use App\Entity\Demandeur;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AboTotalInsti|null find($id, $lockMode = null, $lockVersion = null)
 * @method AboTotalInsti|null findOneBy(array $criteria, array $orderBy = null)
 * @method AboTotalInsti[]    findAll()
 * @method AboTotalInsti[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AboTotalInstiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AboTotalInsti::class);
    }


    /**
     * @param Demandeur $demandeur
     * @param DateTimeImmutable $date
     * @return AboTotalInsti[]
     */
    public function findAbonnement(Demandeur $demandeur,DateTimeImmutable $date){
        return $this->createQueryBuilder('ati')
            ->andWhere('ati.demandeur = :demandeur')
            ->andWhere('ati.debut <= :date')
            ->andWhere('ati.fin >= :date')
            ->setParameter('date',$date)
            ->setParameter('demandeur',$demandeur)
            ->getQuery()
            ->getResult();
    }

    public function finAnonnementGrandCompte():array{
        return $this->createQueryBuilder('ati')
            ->leftJoin('ati.demandeur','demandeur')
            ->leftJoin('demandeur.agents','agent')
            ->leftJoin('agent.user','user')
            ->andWhere('user.roles LIKE :role AND user.roles LIKE :rolegc')
            ->andWhere('ati.abonne = :abonne')
            ->setParameter('role','%"'.'ROLE_MANITOU'.'"%')
            ->setParameter('rolegc','%"'.'ROLE_GRANDCOMPTE'.'"%')
            ->setParameter('abonne',true)
            ->getQuery()
            ->getResult()
            ;
    }

    public function finForPrelevement(DateTime $date){
        return $this->createQueryBuilder('ati')
            ->leftJoin('ati.demandeur','demandeur')
            ->leftJoin('demandeur.agents','agent')
            ->leftJoin('agent.user','user')
            ->andWhere('ati.fin > :date')
            ->andWhere('ati.abonne = :abonne')
            ->andWhere('user.roles LIKE :role')
            ->setParameter('role','%"'.'ROLE_GRANDCOMPTE'.'"%')
            ->setParameter('date',$date)
            ->setParameter('abonne',true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Demandeur $demandeur
     * @return AboTotalInsti|null
     * @throws NonUniqueResultException
     */
    public function findAbonnementClassique(Demandeur $demandeur):?AboTotalInsti{
        return $this->createQueryBuilder('abo_total_insti')
            ->leftJoin('abo_total_insti.abonnement','abonnement')
            ->andWhere('abonnement.profil = :profil')
            ->andWhere('abo_total_insti.abonne = :abonne')
            ->andWhere('abo_total_insti.demandeur = :demandeur')
            ->setParameter('demandeur',$demandeur)
            ->setParameter('profil',$demandeur->getProfil())
            ->setParameter('abonne',true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
