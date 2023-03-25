<?php

namespace App\Repository;

use App\Entity\TvaSiret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TvaSiret|null find($id, $lockMode = null, $lockVersion = null)
 * @method TvaSiret|null findOneBy(array $criteria, array $orderBy = null)
 * @method TvaSiret[]    findAll()
 * @method TvaSiret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TvaSiretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TvaSiret::class);
    }


}
