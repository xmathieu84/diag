<?php

namespace App\Repository;

use App\Entity\Abonnements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method Abonnements|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnements|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnements[]    findAll()
 * @method Abonnements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnements::class);
    }


    /**
     * @param Integer $idEntreprise
     * @return Abonnements|null
     * @throws NonUniqueResultException
     */
    public function findByEntreprise(Int $idEntreprise): ?Abonnements
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.etatAbonnements', 'ea')
            ->leftJoin('ea.entreprise', 'e')
            ->andWhere('e.id = :idEntreprise')
            ->andWhere('ea.abonne = 1')
            ->setParameter('idEntreprise', $idEntreprise)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findForStatOTD()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.etatAbonnements', 'ea')
            ->andWhere('ea.abonne = :abonne')
            ->andWhere('a.cible = :cible')
            ->setParameter('abonne', true)
            ->setParameter('cible', 'otd')
            ->getQuery()
            ->getResult();
    }

    public function findForStatInsti()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.etatAbonnements', 'ea')
            ->andWhere('ea.abonne = :abonne')
            ->andWhere('a.cible = :cible')
            ->setParameter('abonne', true)
            ->setParameter('cible', 'otd')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param float $prix
     * @return Abonnements[]
     */
    public function findForChange(float $prix):array{
        return $this->createQueryBuilder('a')
            ->andWhere('a.prix > :prix')
            ->andWhere('a.cible = :cible')
            ->setParameter('cible','otd')
            ->setParameter('prix',$prix)
            ->getQuery()
            ->getResult();
    }
}
