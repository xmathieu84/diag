<?php

namespace App\Repository;

use App\Entity\ReonseForum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReonseForum|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReonseForum|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReonseForum[]    findAll()
 * @method ReonseForum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReonseForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReonseForum::class);
    }


}
