<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\ReponseAo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReponseAo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponseAo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponseAo[]    findAll()
 * @method ReponseAo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseAoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponseAo::class);
    }

    // /**
    //  * @return ReponseAo[] Returns an array of ReponseAo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReponseAo
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function  findByStatuEntrepriseDate(Entreprise $entreprise,\DateTimeInterface $date,string $statut):array{
        return $this->createQueryBuilder('r')
            ->leftJoin('r.appel','a')
            ->andWhere('r.entreprise = :entreprise')
            ->andWhere('a.DateRemiseProp >= :date')
            ->andWhere('a.etat = :statut')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('date',$date)
            ->setParameter('statut',$statut)
            ->getQuery()
            ->getResult();
    }

    public function findByEntreprise(Entreprise $entreprise){
        return $this->createQueryBuilder('reponseAo')
            ->leftJoin('reponseAo.appel','appel')
            ->andWhere('appel.etat = :etat')
            ->andWhere('reponseAo.entreprise = :entreprise')
            ->setParameter('entreprise',$entreprise)
            ->setParameter('etat','reponseChoisie')
            ->getQuery()
            ->getResult();
    }
}
