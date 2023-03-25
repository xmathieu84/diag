<?php

namespace App\Repository;

use App\Entity\ListeInterTypeInter;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListeInterTypeInter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListeInterTypeInter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListeInterTypeInter[]    findAll()
 * @method ListeInterTypeInter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListeInterTypeInterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListeInterTypeInter::class);
    }

    public function findForAdmin():array{
        return $this->createQueryBuilder('liti')
            ->leftJoin('liti.listeInter','liste_inter')
            ->orderBy('liste_inter.id','ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @return ListeInterTypeInter[]
     */
    public function findBySalarie(Salarie $salarie):array{
        return $this->createQueryBuilder('liti')
            ->leftJoin('liti.tauxHoraires','tauxHoraires')
            ->andWhere('tauxHoraires.salarie = :salarie')
            ->setParameter('salarie',$salarie)
            ->getQuery()
            ->getResult();
    }
}
