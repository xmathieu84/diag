<?php

namespace App\Repository;

use App\Entity\DocBanque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocBanque|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocBanque|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocBanque[]    findAll()
 * @method DocBanque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocBanqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocBanque::class);
    }


}
