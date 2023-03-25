<?php

namespace App\Repository;

use App\Entity\TypInter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypInter|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypInter|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypInter[]    findAll()
 * @method TypInter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypInterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypInter::class);
    }


}
