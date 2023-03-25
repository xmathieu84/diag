<?php

namespace App\Repository;

use App\Entity\Demandeur;
use App\Entity\FactureInsti;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureInsti|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureInsti|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureInsti[]    findAll()
 * @method FactureInsti[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureInstiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureInsti::class);
    }



    /**
     * @param Demandeur $institution
     * @param DateTimeImmutable $dateDebut
     * @param DateTimeImmutable $dateFin
     * @return FactureInsti[]
     */
    public function findByDateInsti(Demandeur $institution,DateTimeImmutable $dateDebut,DateTimeImmutable $dateFin):array{
        return $this->createQueryBuilder('f')
            ->andWhere('f.institution = :institution')
            ->andWhere('f.date >= :dateDebut')
            ->andWhere('f.date <= :dateFin')
            ->setParameter('institution', $institution)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Demandeur $institution
     * @return FactureInsti []
     */
    public function findByMandat(Demandeur $institution): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.institution', 'i')
            ->leftJoin('i.aboTotalInstis', 'a')
            ->leftJoin('f.packSup', 'p')
            ->andWhere('f.institution = :institution')
            ->andWhere('a.mandatRecu = :mandat')
            ->orWhere('p.mandatRecu = :mandat')
            ->setParameter('mandat', false)
            ->setParameter('institution', $institution)
            ->getQuery()
            ->getResult();

    }

    public function findByRole(\DateTime $debut,\DateTime $fin,string $role):array{
        return $this->createQueryBuilder('facture_insti')
            ->leftJoin('facture_insti.institution','institution')
            ->leftJoin('institution.agents','agents')
            ->leftJoin('agents.user','user')
            ->andWhere('user.roles LIKE :roleType AND user.roles LIKE :roleChef')
            ->andWhere('facture_insti.date >= :debut')
            ->andWhere('facture_insti.date <= :fin')
            ->setParameter('roleChef','%"'.'ROLE_MANITOU'.'"%')
            ->setParameter('roleType','%"'.$role.'"%')
            ->setParameter('debut',$debut)
            ->setParameter("fin",$fin)
            ->getQuery()
            ->getResult();
    }


}
