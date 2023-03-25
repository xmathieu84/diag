<?php

namespace App\Repository;

use App\Entity\Annotation;
use App\Entity\Dossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annotation[]    findAll()
 * @method Annotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annotation::class);
    }

    // /**
    //  * @return Annotation[] Returns an array of Annotation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annotation
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param int $id
     * @return Annotation[]
     */
    public function findByDoc(int $id):array{
        return $this->createQueryBuilder('a')
            ->leftJoin('a.docSousDossier','ss')
            ->andWhere('ss.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }

    public function findByDossier(Dossier $dossier){
        return $this->createQueryBuilder('a')
            ->leftJoin('a.docSousDossier','ds')
            ->leftJoin('ds.sousDossier','sousDossier')
            ->andWhere('sousDossier.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->orderBy('a.date','DESC')
            ->getQuery()
            ->getResult();
    }
}
