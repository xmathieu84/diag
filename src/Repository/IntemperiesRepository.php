<?php

namespace App\Repository;

use App\Entity\Intemperies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Intemperies|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intemperies|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intemperies[]    findAll()
 * @method Intemperies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntemperiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intemperies::class);
    }


}
