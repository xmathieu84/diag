<?php

namespace App\Repository;

use App\Entity\MailPrefecture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MailPrefecture|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailPrefecture|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailPrefecture[]    findAll()
 * @method MailPrefecture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailPrefectureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailPrefecture::class);
    }


}
