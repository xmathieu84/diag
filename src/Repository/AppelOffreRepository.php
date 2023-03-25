<?php

namespace App\Repository;

use App\Entity\AppelOffre;
use App\Entity\Demandeur;
use App\Entity\Entreprise;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppelOffre|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppelOffre|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppelOffre[]    findAll()
 * @method AppelOffre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppelOffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppelOffre::class);
    }


    /**
     * @param Demandeur $institution
     *
     * @return AppelOffre[]
     */
    public function findByInstitution(Demandeur $institution):array{
        return $this->createQueryBuilder('ao')
            ->leftJoin('ao.agents','a')
            ->andWhere('a.demandeur = :institution')

            ->setParameter('institution',$institution)
            ->orderBy('ao.date','DESC')
            ->getQuery()
            ->getResult();
    }
    /**
     * @param Demandeur $institution
     * @param string|null $etat
     * @return AppelOffre[]
     */
    public function findByEtat(Demandeur $institution,string $etat = null):array{
        return $this->createQueryBuilder('ao')
            ->leftJoin('ao.agents','a')
            ->andWhere('a.demandeur = :institution')
            ->andWhere('ao.etat = :etat ')
            ->setParameter('institution',$institution)
            ->setParameter('etat',$etat)
            ->orderBy('ao.date','DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $date
     * @param string $etat
     * @return int|mixed|string
     */
    public function findForOtd(DateTimeImmutable $date,string $etat){
        return $this->createQueryBuilder('ao')
            ->andWhere('ao.DateRemiseProp >= :date')
            ->andWhere('ao.etat = :etat')
            ->setParameter('etat',$etat)
            ->setParameter('date',$date)
            ->orderBy('ao.DateRemiseProp','DESC')
            ->getQuery()
            ->getResult();
    }

    public function nombreAppelByEntreprise(Entreprise $entreprise):array{
        return $this->createQueryBuilder('ao')
            ->leftJoin('ao.reponseAos','reponseAos')
            ->andWhere('reponseAos.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }

    public function nobreAoChoisi(Entreprise $entreprise):array{
        return $this->createQueryBuilder('ao')
            ->leftJoin('ao.reponseChoisie','reponseChoisie')
            ->andWhere('reponseChoisie.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }
}
