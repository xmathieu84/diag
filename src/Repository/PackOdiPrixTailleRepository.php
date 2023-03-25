<?php

namespace App\Repository;

use App\Entity\Pack;
use App\Entity\PackOdiPrixTaille;
use App\Entity\Salarie;
use App\Entity\TailleBien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PackOdiPrixTaille|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackOdiPrixTaille|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackOdiPrixTaille[]    findAll()
 * @method PackOdiPrixTaille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackOdiPrixTailleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackOdiPrixTaille::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PackOdiPrixTaille $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(PackOdiPrixTaille $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Pack $pack
     * @param TailleBien $taille
     * @return PackOdiPrixTaille[]
     */
    public function findByPackTaille(Pack $pack,TailleBien $taille):array{
        return $this->createQueryBuilder('packOdiPrixTaille')
            ->leftJoin('packOdiPrixTaille.packOdi','packOdi')
            ->andWhere('packOdiPrixTaille.taille = :taille')
            ->andWhere('packOdi.pack = :pack')
            ->setParameter('pack',$pack)
            ->setParameter('taille',$taille)
            ->getQuery()
            ->getResult();
    }

    public function findForInter(TailleBien $tailleBien,Salarie $salarie){

        return $this->createQueryBuilder('packOdiPrixTaille')
            ->leftJoin('packOdiPrixTaille.packOdi','packOdi')
            ->andWhere('packOdi.odi = :salarie')
            ->andWhere('packOdiPrixTaille.taille = :taille')
            ->setParameter('salarie',$salarie)
            ->setParameter('taille',$tailleBien)
            ->getQuery()
            ->getResult();
    }
}
