<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * Undocumented function
     *
     * @param string $mail
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findOneByMailEntreprise(string $mail): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->andWhere(' u.user_ent IS NOT NULL')
            ->setParameter('val', $mail)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Undocumented function
     *
     * @param string $mail
     * @return void
     * @throws NonUniqueResultException
     */
    public function findOneByMailDemandeur(string $mail)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->andWhere(' u.user_dem IS NOT NULL')
            ->setParameter('val', $mail)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Undocumented function
     *
     * @param string $mail
     * @return void
     * @throws NonUniqueResultException
     */
    public function findOneByMailSalarie(string $mail)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->andWhere(' u.salarie IS NOT NULL')
            ->setParameter('val', $mail)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findByRole(){
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role','["'.'ROLE_INSTITUTION'.'"]')
            ->getQuery()
            ->getResult();
    }

    public function findForStatAdmin($role){
        return $this->createQueryBuilder('user')
            ->andWhere('user.roles LIKE :role')
            ->setParameter('role','%"'.$role.'"%')
            ->getQuery()
            ->getResult();
    }
    public function findForStatAdminGc($role,$roleI){
        return $this->createQueryBuilder('user')
            ->andWhere('user.roles LIKE :role')
            ->andWhere('user.roles LIKE :roleI')
            ->setParameter('role','%"'.$role.'"%')
            ->setParameter('roleI','%"'.$roleI.'"%')
            ->getQuery()
            ->getResult();
    }
}
