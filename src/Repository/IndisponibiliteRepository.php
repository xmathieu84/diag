<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Indisponibilite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Indisponibilite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indisponibilite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indisponibilite[]    findAll()
 * @method Indisponibilite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indisponibilite::class);
    }

    public function findByEntreprise(Entreprise $entreprise){
        return $this->createQueryBuilder('indisponibilite')
            ->leftJoin('indisponibilite.salarie','salarie')
            ->andWhere('salarie.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }
}
