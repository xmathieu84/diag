<?php

namespace App\Repository;

use App\Entity\Agent;
use App\Entity\Demandeur;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agent[]    findAll()
 * @method Agent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agent::class);
    }

    public function findResponsable( $institution){
        return $this->createQueryBuilder('agent')
            ->leftJoin('agent.user','user')
            ->andWhere('agent.demandeur = :demandeur')
            ->andWhere("user.roles LIKE :role or user.roles  LIKE :roleBtp")
            ->setParameter('demandeur',$institution)
            ->setParameter('role','%"'.'ROLE_MANITOU'.'"%')
            ->setParameter('roleBtp','%"'.'ROLE_BTP'.'"%')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $ville
     * @return Agent[]
     */
    public function findForSyndic(string $ville):array{
        return $this->createQueryBuilder('agent')
            ->leftJoin('agent.demandeur','demandeur')
            ->leftJoin('demandeur.adresse','adresse')
            ->andWhere('demandeur.profil = :profil')
            ->andWhere('adresse.ville = :ville')
            ->andWhere('demandeur.acces IS NULL')
            ->setParameter('profil','Syndicat de co-propriété')
            ->setParameter('ville',$ville)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Agent $agent
     * @return array
     * @throws NonUniqueResultException
     */
    public function findSyndic(Agent $agent){
        return $this->createQueryBuilder('agent')
            ->leftJoin('agent.user','user')
            ->leftJoin('agent.superieur','superieur')
            ->andWhere('superieur = :sup')
            ->andWhere('user.roles LIKE :role')
            ->setParameter('role','%"'.'ROLE_SYNDIC'.'"%')
            ->setParameter('sup',$agent)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $email
     * @return Agent[]
     *
     */
    public function findResponsableGc(string $email):array{
        return $this->createQueryBuilder('agent')
            ->leftJoin('agent.user','user')
            ->andWhere('user.email LIKE :email')
            ->andWhere('user.roles LIKE :roleD')
            ->andWhere('user.roles LIKE :roleGc')
            ->setParameter('roleD','%"'.'ROLE_MANITOU'.'"%')
            ->setParameter('roleGc','%"'.'ROLE_GRANDCOMPTE'.'"%')
            ->setParameter('email','%"'.$email.'"%')
            ->getQuery()
            ->getResut();
    }

    /**
     * @param Demandeur $institution
     * @param string $role
     * @return Agent[]
     *
     */
    public function findSubaltern(Demandeur $institution,string $role):array{
        return $this->createQueryBuilder('agent')
            ->leftJoin('agent.user','user')
            ->andWhere('user.roles LIKE  :role')
            ->andWhere('agent.demandeur = :institution')
            ->setParameter('role','%"'.$role.'"%')
            ->setParameter('institution',$institution)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Demandeur $demandeur
     * @return Agent
     * @throws NonUniqueResultException
     */
    public function findProBtp(Demandeur $demandeur):Agent{
        return $this->createQueryBuilder('agent')
            ->leftJoin('agent.user','user')
            ->andWhere('user.roles LIKE  :role')
            ->andWhere('agent.demandeur = :institution')
            ->setParameter('role','%"'.'ROLE_BTP'.'"%')
            ->setParameter('institution',$demandeur)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $role
     * @return Agent[]
     */
    public function findConnecte(string $role):array{
        return $this->createQueryBuilder('agent')
            ->leftJoin('agent.user','user')
            ->andWhere('user.isConnect = :connect')
            ->andWhere('user.roles LIKE  :role')
            ->setParameter('role','%"'.$role.'"%')
            ->setParameter('connect',true)
            ->getQuery()
            ->getResult();
    }






}
