<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\MailPrefecture;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entreprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entreprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entreprise[]    findAll()
 * @method Entreprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }


    /**
     * @param DateTimeInterface $value
     * @param DateTimeInterface $date
     * @return Entreprise[]
     */
    public function findByFinAbonnement(DateTimeInterface $value, DateTimeInterface $date):array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateFinAbonnement < :val')
            ->andWhere('e.dateFinAbonnement > :date')
            ->andWhere('e.abonnements IS NOT NULL')
            ->setParameter('val', $value)
            ->setParameter('date', $date)
            ->orderBy('e.dateFinAbonnement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeInterface $value
     * @return Entreprise[]
     */
    public function findByAbonnementTermine(DateTimeInterface $value):array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateFinAbonnement <= :val')
            ->andWhere('e.abonnements IS NOT NULL')
            ->setParameter('val', $value)
            ->orderBy('e.dateFinAbonnement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return Entreprise[]
     */
    public function findByMandatCerfa(DateTimeInterface $dateDebut,DateTimeInterface $dateFin):array{
        return  $this->createQueryBuilder('e')
            ->leftJoin('e.mandatCerfas','mandat')
            ->andWhere('mandat.date >= :dateDebut')
            ->andWhere('mandat.date <= :dateFin')
            ->setParameter('dateDebut',$dateDebut)
            ->setParameter('dateFin',$dateFin)
            ->getQuery()
            ->getResult();

    }

    /**
     * @param DateTimeInterface $dateDebut
     * @param DateTimeInterface $dateFin
     * @return Entreprise[]
     */
    public  function findByInterInsti(DateTimeInterface $dateDebut,DateTimeInterface $dateFin):array{
        return  $this->createQueryBuilder('e')
            ->leftJoin('e.salaries','salarie')
            ->leftJoin('salarie.reservations','reservation')
            ->leftJoin('reservation.intervention','intervention')
            ->leftJoin('intervention.intDem','dem')
            ->leftJoin('dem.user','u')
            ->andWhere('u.roles LIKE :role')
            ->andWhere('intervention.statuInter = :statut')
            ->andWhere('reservation.debut > :dateDebut')
            ->andWhere('reservation.debut < :dateFin')
            ->setParameter('statut', 'termine')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('role', '["' . 'ROLE_INSTITUTION' . '"]')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $role
     * @return Entreprise []
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->andWhere('u.role LIKE :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $code
     * @param MailPrefecture $departement
     * @return Entreprise[]
     */
    public function findAmbassadeur(string $code,MailPrefecture $departement):array{
        return $this->createQueryBuilder('entreprise')
            ->leftJoin('entreprise.adresse','adresse')
            ->leftJoin('entreprise.ambassadeur','ambassadeur')
            ->andWhere('adresse.departement = :departement')
            ->andWhere('ambassadeur.codeReduc = :code')
            ->setParameter('code',$code)
            ->setParameter('departement',$departement)
            ->getQuery()
            ->getResult();
    }


}
