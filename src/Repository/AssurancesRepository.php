<?php

namespace App\Repository;

use App\Entity\Assurances;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Assurances|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assurances|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assurances[]    findAll()
 * @method Assurances[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssurancesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assurances::class);
    }


}
