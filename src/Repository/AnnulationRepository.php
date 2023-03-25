<?php

namespace App\Repository;

use App\Entity\Annulation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annulation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annulation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annulation[]    findAll()
 * @method Annulation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnulationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annulation::class);
    }


}
