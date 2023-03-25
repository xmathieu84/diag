<?php

namespace App\Repository;

use App\Entity\ProBtp;
use App\Entity\Travaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProBtp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProBtp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProBtp[]    findAll()
 * @method ProBtp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProBtpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProBtp::class);
    }

    /**
     * @param Travaux $travaux
     * @param float $lat
     * @param float $lon
     * @param string $type
     * @param int|null $limite
     * @return array
     */
   public function findForPubcibleInter(Travaux $travaux,float $lat,float $lon,string $type, int $limite=null):array{

        return $this->createQueryBuilder('pb')
            ->leftJoin('pb.travaux','travaux')
            ->leftJoin('pb.demandeur','demandeur')
            ->leftJoin('demandeur.aboTotalInstis','aboTotalInstis')
            ->leftJoin('aboTotalInstis.abonnement','abonnement')
            ->leftJoin('demandeur.adresse','adresse')
            ->leftJoin('adresse.coordonnees','coordonnees')
            ->andWhere('travaux = :trvx')
            ->andWhere('abonnement.cible = :cible')
            ->andWhere('aboTotalInstis.abonne = :abonne')
            ->andWhere('coordonnees.latMinInter <= :latitude')
            ->andWhere('coordonnees.latMaxInter >= :latitude')
            ->andWhere('coordonnees.lonMinInter <= :longitude')
            ->andWhere('coordonnees.lonMaxInter >= :longitude')
            ->setParameter('trvx',$travaux)
            ->setParameter('latitude',$lat)
            ->setParameter('longitude',$lon)
            ->setParameter('cible',$type)
            ->setParameter('abonne',true)
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
   }

   public function findByTravaux(Travaux $travaux):array{
        return $this->createQueryBuilder('pb')
            ->leftJoin('pb.travaux','travaux')
            ->leftJoin('pb.demandeur','demandeur')
            ->leftJoin('demandeur.aboTotalInstis','aboTotalInstis')
            ->leftJoin('aboTotalInstis.abonnement','abonnement')
            ->andWhere('abonnement.cible = :cible')
            ->andWhere('aboTotalInstis.abonne = :abonne')
            ->andWhere('travaux = :travail')
            ->setParameter('travail',$travaux)
            ->setParameter('abonne',true)
            ->setParameter('cible','premium')
            ->getQuery()
            ->getResult();
   }
}
