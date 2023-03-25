<?php

namespace App\Repository;

use App\Entity\DocSousDossier;
use App\Entity\Dossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocSousDossier|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocSousDossier|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocSousDossier[]    findAll()
 * @method DocSousDossier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocSousDossierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocSousDossier::class);
    }


    /**
     * @param Dossier $dossier
     * @return DocSousDossier []
     */
    public function findByDossier(Dossier $dossier): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.sousDossier', 'sousDossier')
            ->andWhere('sousDossier.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();
    }



}
