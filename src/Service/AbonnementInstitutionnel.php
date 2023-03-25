<?php


namespace App\Service;


use App\Entity\AboTotalInsti;
use App\Entity\Ambassadeur;
use App\Entity\CodePromo;
use App\Entity\Demandeur;
use App\Repository\AbonnementGciRepository;

class AbonnementInstitutionnel
{
    protected DefinirDate $definirDate;
    protected AbonnementGciRepository $abonnementGciRepository;
    /**
     * AbonnementInstitutionnel constructor.
     * @param DefinirDate $definirDate
     */
    public function __construct(DefinirDate $definirDate,AbonnementGciRepository $abonnementGciRepository)
    {
        $this->definirDate = $definirDate;
        $this->abonnementGciRepository = $abonnementGciRepository;
    }

    /**
     * @param Demandeur $demandeur
     * @param int|null $habitant
     * @return AboTotalInsti
     * @throws \Exception
     */
    public function choixAbonnement(Demandeur $demandeur,int $habitant=null){
        $abonnement = new AboTotalInsti();
        if (!$habitant){
            $profil = $demandeur->getProfil();
        }
        else{
            $profil = 'insti';
        }
        $abonnementI = $this->abonnementGciRepository->abonnementInsti($habitant,$profil);
       
        $debut = $this->definirDate->aujourdhuiImmutable();
        $fin = $this->definirDate->aujourdhuiImmutable()->add($abonnementI->getDuree());
        $abonnement->setAbonne(true)
            ->setDebut($debut)
            ->setFin($fin)
            ->setDemandeur($demandeur)
            ->setAbonnement($abonnementI)
            ->setTotal($abonnementI->getPrix()*1.2);
        return $abonnement;
    }

    /**
     * @param Demandeur $demandeur
     * @param int $utilisateur
     * @return AboTotalInsti
     * @throws \Exception
     */
    public function choixAbonnementGc(Demandeur $demandeur,int $utilisateur,$profil){

        $abonnement = new AboTotalInsti();
        $abonnementI = $this->abonnementGciRepository->abonnementGc($profil,$utilisateur);

        $debut = $this->definirDate->aujourdhuiImmutable();
        $fin = $this->definirDate->aujourdhuiImmutable()->add($abonnementI->getDuree());
        $abonnement->setAbonne(true)
            ->setDebut($debut)
            ->setFin($fin)
            ->setDemandeur($demandeur)
            ->setAbonnement($abonnementI)
            ->setTotal($abonnementI->getPrix()*1.2);
        return $abonnement;
    }

    public function retourAbonnementI(int $habitant = null,$profil){
            $abonnement = $this->abonnementGciRepository->abonnementInsti($habitant,$profil);
        return $abonnement;
    }


    public function retourAbonnementGc($utilisateur,$profil){
        $abonnement = $this->abonnementGciRepository->abonnementGc($profil,$utilisateur);

        return $abonnement;
    }

    public function abonnementInstiPromo(CodePromo $code,Demandeur $demandeur){
        $abonnement = new AboTotalInsti();
        $debut = $this->definirDate->aujourdhuiImmutable();
        $fin = $this->definirDate->aujourdhuiImmutable()->add(new \DateInterval('P1Y'));
        $abonnement->setAbonne(true)
            ->setDebut($debut)
            ->setFin($fin)
            ->setDemandeur($demandeur)
            ->setAbonnement($code->getAbonnementGci())
            ->setTotal(($code->getRemise()/100)*$code->getAbonnementGci()->getPrix()*1.2);
        return $abonnement;
    }

    public function ambassadeurGrandCompte(Ambassadeur $ambassadeur,Demandeur $demandeur){
        $abonnement = new AboTotalInsti();
        $debut = $this->definirDate->aujourdhuiImmutable();
        $fin = $this->definirDate->aujourdhuiImmutable()->add($ambassadeur->getDureeAbo());
        $abonnement->setAbonne(true)
            ->setDebut($debut)
            ->setFin($fin)
            ->setDemandeur($demandeur)
            ->setAbonnement($ambassadeur->getAbonnementGci())
            ->setTotal($ambassadeur->getPrix());
        return $abonnement;
    }
}