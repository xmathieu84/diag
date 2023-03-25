<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }


    /**
     * @param string $statut1
     * @param string $destinatire
     * @return Message[]
     */
    public function archive(string $statut1, string $destinatire):array
    {
        return $this->createQueryBuilder('m')
            ->where('m.statut != :stat')
            ->andWhere('m.destinataire = :dest')
            ->setParameter('stat', $statut1)
            ->setParameter('dest', $destinatire)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
