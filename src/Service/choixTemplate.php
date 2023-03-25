<?php

namespace App\Service;

use App\Entity\Entreprise;
use App\Entity\LicenceDgac;
use App\Entity\User;
use App\Helper\DemandeurRepoTrait;
use App\Repository\EntrepriseRepository;
use App\Repository\SalarieRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Helper\AgentRepoTrait;

/**
 * Class choixTemplate
 * @package App\Service
 */
class choixTemplate
{
    use DemandeurRepoTrait,AgentRepoTrait;
    /**
     * @var SalarieRepository
     */
    private $salarieRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private EntrepriseRepository $entrepriseRepository;

    /**
     * choixTemplate constructor.
     * @param SalarieRepository $salarieRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(SalarieRepository $salarieRepository,  TokenStorageInterface $tokenStorage,EntrepriseRepository $entrepriseRepository)
    {
        $this->salarieRepository = $salarieRepository;
        $this->tokenStorage = $tokenStorage;
        $this->entrepriseRepository =$entrepriseRepository;

    }

    /**
     * Renvoie un template different selon la forme Juridique de l'entreprise
     * Renvoie l'objet salarie d'un auto-entrepreneur
     *
     * @param Entreprise $entreprise
     * @return string
     */
    public function templateAE(Entreprise $entreprise): string
    {
        if ($entreprise->getCgv() === false) {
            $template = 'entreprise/terminerInscription.html.twig';

        } else {
            $template = 'entreprise/baseAE.html.twig';
        }
        return $template;
    }

    /**
     * Renvoie un template different selon la forme Juridique de l'entreprise
     * Renvoie l'objet salarie d'un auto-entrepreneur
     * Utilisé après la finalisation de l'inscription
     *
     * @param Entreprise $entreprise
     * @return array
     */
    public function templateLicence(Entreprise $entreprise): array
    {
        $salarie = $this->salarieRepository->findOneBy(['entreprise' => $entreprise]);
        if ($entreprise->getCgv() === true) {

            if ($entreprise->getFormJuridique() == 'auto-entrepreneur') {
                $template = 'entreprise/baseAE.html.twig';
                $Licence = $salarie->getLicenceDgac();
            } else {
                $template = 'entreprise/baseentreprise.html.twig';
            }
        } else {
            if ($entreprise->getFormJuridique() == 'auto-entrepreneur') {
                $Licence = new LicenceDgac();
                $salarie->setLicenceDgac($Licence);
            }

            $template = 'entreprise/terminerInscription.hml.twig';
        }
        return [$salarie, $template];
    }

    /**
     * @param mixed $user
     * @return string
     */
    public function templateSalEnt(User $user):string
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $entreprise = $this->entrepriseRepository->findOneBy(['user'=>$user]);
        if ($salarie) {
            $template = 'salarie/basesalarie.html.twig';
        } elseif ($entreprise->getFormJuridique() == 'auto-entrepreneur') {
            $template = 'entreprise/baseAE.html.twig';
        } else {
            $template = 'entreprise/baseentreprise.html.twig';
        }

        return $template;
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return string
     */
    public function DefinirNom(User $user): string
    {
        if ($user->getUserEnt()) {
            $auteur = $user->getUserEnt()->getDirigeant()->getPrenom() . ' ' . $user->getUserEnt()->getDirigeant()->getNom();
        } else {
            $auteur = $user->getSalarie()->getCivilite()->getPrenom() . ' ' . $user->getUserEnt()->getDirigeant()->getNom();
        }
        return $auteur;
    }

    /**
     * Undocumented function
     *
     * @param  $user
     * @return array
     */
    public function templateSal($user)
    {
        $entreprise = $this->entrepriseRepository->findOneBy(['user'=>$user]);
        if ($entreprise) {
            $salarie = $this->salarieRepository->findOneBy(['entreprise' => $entreprise]);
            $template = 'entreprise/baseAE.html.twig';
        } else {
            $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
            $template = 'salarie/basesalarie.html.twig';
        }
        return [$salarie, $template];
    }

    /**
     * @return string
     */
    public function templateOnglet():string
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user) {
            $template = 'accueil/baseaccueil.html.twig';
        } elseif ($user) {
            if ($user->getUserDem()) {
                $template = 'demandeur/basedemandeur.html.twig';
            }
        }
        return $template;
    }

    /**
     * Détermination du template entre demandeur classique et demandeur institutionnel
     *
     * @param UserInterface $user
     * @return array
     */
    public function templateDem(UserInterface $user,string $code =null): array
    {
            if ($user->hasRole('ROLE_INSTITUTION') ||$user->hasRole('ROLE_GRANDCOMPTE') ||$user->hasRole('ROLE_BTP')){
                $template = 'institution/baseInsti.html.twig';

                if (!$code){
                    $agent = $user->getAgent();
                }
                else{
                    $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
                }

                $demandeur = $agent->getDemandeur();

            }

            else{
                $template = 'demandeur/basedemandeur.html.twig';
                $demandeur = $this->demandeurRepository->findOneBy(['user'=>$user]);
            }


        return [$template,$demandeur];
    }

    /**
     * @param User $user
     * @return array
     */
    public function definirConnecte(User $user):array{
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $entreprise = $this->entrepriseRepository->findOneBy(['user'=>$user]);
        if ($salarie){
            $conecte = $salarie;
            $civilite = $salarie->getCivilite();
        }
        else{
            $conecte = $entreprise;
            $civilite = $entreprise->getDirigeant();
        }
        return [$conecte,$civilite];
    }

    /***
     * @param User $user
     * @return array
     */
    public function definirTemplateEntrepriseSalarie(User $user):array{
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $entreprise = $this->entrepriseRepository->findOneBy(['user'=>$user]);
        if ($salarie) {
            $template = 'salarie/basesalarie.html.twig';
            $otd = $salarie->getEntreprise();
        } elseif ($entreprise->getFormJuridique() == 'auto-entrepreneur') {
            $template = 'entreprise/baseAE.html.twig';
            $otd = $entreprise;
        } else {
            $template = 'entreprise/baseentreprise.html.twig';
            $otd = $entreprise;
        }
        return [$template,$otd];
    }

    /**
     * @param UserInterface $user
     * @return string
     */
    public function templateSepa(UserInterface $user){
        if ($user->getAgent() && $user->getAgent()->getDemandeur()->getCgv()){
            $template = 'institution/baseInsti.html.twig';
        }
        elseif ($user->getSalarie()->getEntreprise()->getCgv() && $user->getSalarie()){
            $template = 'entreprise/baseAE.html.twig';
        }
        else{
            $template = 'entreprise/terminerInscription.html.twig';
        }
        return $template;
    }

    /**
     * @param User $user
     * @return string
     */
    public function templateCg(User $user){
        if ($user->hasRole('ROLE_INSTITUTION') || $user->hasRole('ROLE_GRANDCOMPTE')){
            return 'institution/baseInsti.html.twig';
        }
        elseif ($user->hasRole('ROLE_ENTREPRISE')|| $user->hasRole('ROLE_SALARIE')){
            return 'entreprise/baseAE.html.twig';
        }
        else{
            return  'demandeur/basedemandeur.html.twig';
        }
    }


    public function templateDiag(User $user=null):string{
        if (!$user){
            return "accueil/baseaccueil.html.twig";
        } elseif ($user->hasRole('ROLE_DEMANDEUR')){
            return "demandeur/basedemandeur.html.twig";
        }
        else{
            return "institution/baseInsti.html.twig";
        }
    }


}
