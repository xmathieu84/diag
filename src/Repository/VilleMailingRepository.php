<?php

namespace App\Repository;

use App\Entity\VilleMailing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VilleMailing|null find($id, $lockMode = null, $lockVersion = null)
 * @method VilleMailing|null findOneBy(array $criteria, array $orderBy = null)
 * @method VilleMailing[]    findAll()
 * @method VilleMailing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleMailingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VilleMailing::class);
    }


}
