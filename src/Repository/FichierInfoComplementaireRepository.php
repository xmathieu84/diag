<?php

namespace App\Repository;

use App\Entity\AppelOffre;
use App\Entity\FichierInfoComplementaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FichierInfoComplementaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method FichierInfoComplementaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method FichierInfoComplementaire[]    findAll()
 * @method FichierInfoComplementaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichierInfoComplementaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichierInfoComplementaire::class);
    }

    // /**
    //  * @return FichierInfoComplementaire[] Returns an array of FichierInfoComplementaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FichierInfoComplementaire
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param AppelOffre $appelOffre
     * @param string $type
     * @return FichierInfoComplementaire[]
     */
    public function findByAoType(AppelOffre $appelOffre,string $type):array{
        return $this->createQueryBuilder('f')
            ->leftJoin('f.InfoComplementaires','i')
            ->andWhere('i.appelOffres = :appel')
            ->andWhere('i.type = :type')
            ->setParameter('type',$type)
            ->setParameter('appel',$appelOffre)
            ->getQuery()
            ->getResult();
    }
    public function findByAppel(AppelOffre $appel){
        return $this->createQueryBuilder('d')
            ->leftJoin('d.InfoComplementaires','i')
            ->andWhere('i.appelOffres = :appel')
            ->setParameter('appel',$appel)
            ->getQuery()
            ->getResult();
    }

}
