<?php

namespace App\Repository;

use App\Entity\ListeInter;
use App\Entity\Salarie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListeInter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListeInter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListeInter[]    findAll()
 * @method ListeInter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListeInterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListeInter::class);
    }


}
