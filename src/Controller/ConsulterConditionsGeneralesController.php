<?php

namespace App\Controller;

use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\PackSupRepository;
use App\Repository\PourcentageRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConsulterConditionsGeneralesController extends AbstractController
{
    use DemandeurRepoTrait,SalarieRepoTrait,AgentRepoTrait,etatAboRepoTrait,AboTotalInstiRepoTrait;
    /**
     * @Route("/consulter conditions générales",name="consultCg")
     * @Security("is_granted('ROLE_USER')")
     */
    public function consulterCg(PourcentageRepository $repository,PackSupRepository $supRepository,DefinirDate $definirDate,choixTemplate $choixTemplate){

        $user = $this->getUser();
        $template = $choixTemplate->templateCg($user);
        if ($user->hasRole('ROLE_INSTITUTION')){
            $pack = $supRepository->findBy(['cible'=>'institution']);
        }else{
            $pack = $supRepository->findBy(['cible'=>'grand compte']);
        }
        $demandeur = $this->demandeurRepository->findOneBy(['user'=>$user]);
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $agent = $this->agentRepository->findOneBy(['user'=>$user]);


        if ($salarie){
            $etat = $this->etatAbonnementRepository->trouverEtat($salarie->getEntreprise(),$definirDate->aujourdhui());
            $dureeOtd = $etat->getDatefin()->diff($etat->getDateDebut())->format('%m%');
            $inscrit = $salarie->getEntreprise();



        }
        elseif($agent){
            $etat = $this->aboTotalInstiRepository->findAbonnement($agent->getDemandeur(),$definirDate->aujourdhuiImmutable());
            $dureeOtd = 12;
            $inscrit = $agent->getDemandeur();

        }
        else{
            $etat=null;
            $dureeOtd = null;
        }
        $cerfa = $repository->findOneBy(['nom'=>'cerfa']);

        return $this->render('accueil/conditionG.html.twig',[
            'template'=>$template,
            'etat'=>$etat,
            'dureeOtd'=>$dureeOtd,
            'cerfa'=>$cerfa->getTaux(),
            'packs'=>$pack
        ]);
    }

}