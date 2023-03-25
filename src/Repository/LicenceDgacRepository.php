<?php

namespace App\Repository;

use App\Entity\LicenceDgac;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LicenceDgac|null find($id, $lockMode = null, $lockVersion = null)
 * @method LicenceDgac|null findOneBy(array $criteria, array $orderBy = null)
 * @method LicenceDgac[]    findAll()
 * @method LicenceDgac[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LicenceDgacRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LicenceDgac::class);
    }


}
