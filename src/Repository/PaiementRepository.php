<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]    findAll()
 * @method Paiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }


}
