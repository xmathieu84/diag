<?php

namespace App\Repository;

use App\Entity\CodePromo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CodePromo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodePromo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodePromo[]    findAll()
 * @method CodePromo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodePromoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodePromo::class);
    }

    /**
     * @param int $habitant
     * @param string $code
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function codePromoByHabitant(int $habitant,string $code,\DateTime $date){
        return $this->createQueryBuilder('codePromo')
            ->leftJoin('codePromo.abonnementGci','abonnementGci')
            ->andWhere('codePromo.codeReduc = :code')
            ->andWhere('codePromo.dateFin > :date')
            ->andWhere('abonnementGci.limiteB <= :habitant')
            ->andWhere('abonnementGci.limiteH >= :habitant')
            ->setParameter('habitant',$habitant)
            ->setParameter('code',$code)
            ->setParameter('date',$date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     * @param \DateTime $date
     * @return CodePromo
     * @throws NonUniqueResultException
     */
    public function findForOtd(string $code, \DateTime $date):?CodePromo{
        return $this->createQueryBuilder('codePromo')
            ->andWhere('codePromo.codeReduc = :code')
            ->andWhere('codePromo.dateFin > :date')
            ->andWhere('codePromo.profil = :profil')
            ->setParameter('date',$date)
            ->setParameter('code',$code)
            ->setParameter('profil','otd')
            ->getQuery()
            ->getOneOrNullResult();
    }


}
