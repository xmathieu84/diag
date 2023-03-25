<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\PackSup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PackSup|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackSup|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackSup[]    findAll()
 * @method PackSup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackSupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackSup::class);
    }

    // /**
    //  * @return PackSup[] Returns an array of PackSup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PackSup
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param Demandeur $institution
     * @return array
     */
    public function findByInstitution(Demandeur $institution):array{
        return $this->createQueryBuilder('pack_sup')
            ->leftJoin('pack_sup.packSupAboInstis','packSupAboInstis')
            ->leftJoin('packSupAboInstis.aboInsti','aboInsti')
            ->andWhere('aboInsti.demandeur = :institution')
            ->setParameter('institution',$institution)
            ->getQuery()
            ->getResult();
    }

    public function findByHabitant(string $profil,int $limiteH,int $limiteB){
        return $this->createQueryBuilder('pack')
           ->andWhere('pack.limiteB <= :limiteB')
            ->andWhere('pack.limiteH >= :limiteH')

            ->setParameter('limiteB',$limiteB)
            ->setParameter('limiteH',$limiteH)

            ->getQuery()
            ->getResult();
    }


}
