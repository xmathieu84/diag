<?php

namespace App\Repository;

use App\Entity\AlphaTango;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AlphaTango|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlphaTango|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlphaTango[]    findAll()
 * @method AlphaTango[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlphaTangoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlphaTango::class);
    }


}
