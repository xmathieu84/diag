<?php

namespace App\Repository;

use App\Entity\Coordonnees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coordonnees|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coordonnees|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coordonnees[]    findAll()
 * @method Coordonnees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoordonneesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coordonnees::class);
    }


}
