<?php

namespace App\Repository;

use App\Entity\CGUvente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CGUvente|null find($id, $lockMode = null, $lockVersion = null)
 * @method CGUvente|null findOneBy(array $criteria, array $orderBy = null)
 * @method CGUvente[]    findAll()
 * @method CGUvente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CGUventeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CGUvente::class);
    }


}
