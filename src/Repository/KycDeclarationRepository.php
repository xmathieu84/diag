<?php

namespace App\Repository;

use App\Entity\KycDeclaration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KycDeclaration|null find($id, $lockMode = null, $lockVersion = null)
 * @method KycDeclaration|null findOneBy(array $criteria, array $orderBy = null)
 * @method KycDeclaration[]    findAll()
 * @method KycDeclaration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KycDeclarationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KycDeclaration::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(KycDeclaration $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(KycDeclaration $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return KycDeclaration[] Returns an array of KycDeclaration objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KycDeclaration
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
