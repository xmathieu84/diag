<?php

namespace App\Repository;

use App\Entity\SepaSigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SepaSigne|null find($id, $lockMode = null, $lockVersion = null)
 * @method SepaSigne|null findOneBy(array $criteria, array $orderBy = null)
 * @method SepaSigne[]    findAll()
 * @method SepaSigne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SepaSigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SepaSigne::class);
    }


}
