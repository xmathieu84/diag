<?php

namespace App\Repository;

use App\Entity\Pourcentage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pourcentage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pourcentage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pourcentage[]    findAll()
 * @method Pourcentage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PourcentageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pourcentage::class);
    }


}
