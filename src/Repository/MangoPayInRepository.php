<?php

namespace App\Repository;

use App\Entity\MangoPayIn;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MangoPayIn|null find($id, $lockMode = null, $lockVersion = null)
 * @method MangoPayIn|null findOneBy(array $criteria, array $orderBy = null)
 * @method MangoPayIn[]    findAll()
 * @method MangoPayIn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangoPayInRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MangoPayIn::class);
    }




    /**
     * Undocumented function
     *
     *
     * @param DatetimeInterface $date
     *
     * @param string $type
     * @return MangoPayIn[]
     */
    public function findByInterPayout(DateTimeInterface$date,string $type): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.intervention IS NOT NULL')
            ->andWhere('m.date <= :date')
            ->andWhere('m.payOut IS NULL')
            ->andWhere('m.type = :type')
            ->setParameter('date', $date)
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return MangoPayIn[]
     */
    public function findByInterEnattente(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.intervention', 'i')
            ->andWhere("i.statuInter = 'en attente de virement'")
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return MangoPayIn[]
     */
    public function paiementDD():array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.intervention IS NOT NULL')

            ->andWhere('m.payOut IS NULL')

            ->getQuery()
            ->getResult();
    }
}
