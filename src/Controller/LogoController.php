<?php


namespace App\Controller;


use App\Helper\AgentRepoTrait;
use App\Helper\PropositionRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\DefinirDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LogoController extends AbstractController
{
    use SalarieRepoTrait,AgentRepoTrait,PropositionRepoTrait;

    /**
     * @return JsonResponse
     * @Route("/institution/menu/logo")
     */
    public function envoieLogo():JsonResponse{
        $reponse = new JsonResponse();
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        if ($agent){
            $logo = $agent->getDemandeur()->getLogo();
        } elseif ($salarie){
            $logo = $salarie->getEntreprise()->getLogo();
        }
        if ($logo){
            $reponse->setData($logo) ;
        }
        else{
            $reponse->setData(null) ;
        }
        return $reponse;
    }

    /**
     * @return JsonResponse
     * @Route ("/entreprise/proposition/nombre")
     * @Security ("is_granted('ROLE_SALARIE')")
     */
    public function propositionMenu(DefinirDate $definirDate):JsonResponse{
        $salarie = $this->getUser()->getSalarie();
        $proposition = $this->propositionRepository->findBySalarieMenu($salarie,$definirDate->aujourdhui());

        return new JsonResponse(count($proposition));
    }
}