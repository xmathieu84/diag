<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\Entreprise;
use App\Entity\Rapport;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Rapport|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rapport|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rapport[]    findAll()
 * @method Rapport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rapport::class);
    }

    /**
     * @param Demandeur $demandeur
     * @return Rapport[]
     */
    public function findByDemandeur(Demandeur $demandeur):array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.intervention', 'i')
            ->andWhere('i.intDem = :demandeur')
            ->setParameter('demandeur', $demandeur)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Salarie $salarie
     * @return Rapport[]
     */
    public function findBySalarie(Salarie $salarie):array{
        return $this->createQueryBuilder('rapport')
            ->leftJoin('rapport.intervention','intervention')
            ->leftJoin('intervention.reservation','reservation')
            ->andWhere('reservation.salarie = :salarie')
            ->setParameter('salarie',$salarie)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Entreprise $entreprise
     * @return Rapport[]
     */
    public function findHdd(Entreprise $entreprise):array{
        return $this->createQueryBuilder('rapport')
            ->leftJoin('rapport.intervention','intervention')
            ->leftJoin('intervention.reservation','reservation')
            ->leftJoin('reservation.salarie','salarie')
            ->andWhere('intervention.statuInter = :statut')
            ->andWhere('intervention.type = :type')
            ->andWhere('salarie.entreprise = :entreprise')
            ->setParameter('statut','termine')
            ->setParameter('type','hdd')
            ->setParameter('entreprise',$entreprise)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Demandeur $demandeur
     * @return Rapport[]
     *
     */
    public function findForEnvoieDemandeur(Demandeur $demandeur):array{
        return $this->createQueryBuilder('rapport')
            ->leftJoin('rapport.intervention','intervention')
            ->andWhere('intervention.intDem = :demandeur')
            ->andWhere('intervention.statuInter = :statut')
            ->setParameter('demandeur',$demandeur)
            ->setParameter('statut','termine')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param UserInterface $user
     * @param int $id
     * @return Rapport|null
     * @throws NonUniqueResultException
     */
    public function findForConsultant(UserInterface $user,int $id):?Rapport{
        return $this->createQueryBuilder('rapport')
            ->leftJoin('rapport.consultant','consultant')
            ->andWhere('consultant.user = :user')
            ->andWhere('rapport.id = :id')
            ->setParameter('id',$id)
            ->setParameter('user',$user)
            ->getQuery()
            ->getOneOrNullResult();
}

    /***
     * @param $id
     * @param Demandeur $demandeur
     * @return Rapport
     * @throws NonUniqueResultException
     */
public function findForDemandeur($id,Demandeur $demandeur):Rapport{
        return $this->createQueryBuilder('rapport')
            ->leftJoin('rapport.intervention','intervention')
            ->andWhere('intervention.intDem = :demandeur')
            ->andWhere('rapport.id = :id')
            ->setParameter('id',$id)
            ->setParameter('demandeur',$demandeur)
            ->getQuery()
            ->getOneOrNullResult();
}


}
