<?php

namespace App\Repository;

use App\Entity\ThemeForum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ThemeForum|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThemeForum|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThemeForum[]    findAll()
 * @method ThemeForum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThemeForum::class);
    }


}
