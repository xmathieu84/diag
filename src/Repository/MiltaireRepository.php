<?php

namespace App\Repository;

use App\Entity\Miltaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Miltaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Miltaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Miltaire[]    findAll()
 * @method Miltaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MiltaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Miltaire::class);
    }


}
