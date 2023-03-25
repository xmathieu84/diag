<?php

namespace App\Repository;

use App\Entity\FichierOTD;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FichierOTD|null find($id, $lockMode = null, $lockVersion = null)
 * @method FichierOTD|null findOneBy(array $criteria, array $orderBy = null)
 * @method FichierOTD[]    findAll()
 * @method FichierOTD[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichierOTDRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichierOTD::class);
    }

    public function findGlobal(float $latMin,float $latMax, float $lonMin,float $lonMax){
        return $this->createQueryBuilder('fichier_otd')
            ->leftJoin('fichier_otd.coorOtd','coor_otd')
            ->andWhere('coor_otd.lat >= :latMin')
            ->andWhere('coor_otd.lat <= :latMax')
            ->andWhere('coor_otd.lon >= :lonMin')
            ->andWhere('coor_otd.lon <= :lonMax')
            ->andWhere('fichier_otd.desabonner = :desabonner')
            ->andWhere('fichier_otd.mail IS NOT NULL')
            ->setParameter('latMin',$latMin)
            ->setParameter('latMax',$latMax)
            ->setParameter('lonMin',$lonMin)
            ->setParameter('lonMax',$lonMax)
            ->setParameter('desabonner','false')
            ->getQuery()
            ->getResult();


    }
}
