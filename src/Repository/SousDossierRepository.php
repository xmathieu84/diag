<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\Dossier;
use App\Entity\SousDossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SousDossier|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousDossier|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousDossier[]    findAll()
 * @method SousDossier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousDossierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousDossier::class);
    }

    // /**
    //  * @return SousDossier[] Returns an array of SousDossier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SousDossier
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByInstitution(Demandeur $institution,string $type,Dossier $dossier){
        return $this->createQueryBuilder('s')
            ->leftJoin('s.dossier','d')
            ->andWhere('d.institution = :institution')
            ->andWhere('s.dossier = :dossier')
            ->andWhere('s.type = :type')
            ->setParameter('institution',$institution)
            ->setParameter('type',$type)
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
