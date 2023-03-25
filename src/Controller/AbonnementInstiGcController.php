<?php


namespace App\Controller;


use App\Helper\RequestTrait;
use App\Service\AbonnementInstitutionnel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementInstiGcController extends AbstractController
{
    use RequestTrait;

    /**
     * @param AbonnementInstitutionnel $abonnementInstitutionnel
     * @return JsonResponse
     * @Route("/abonnementInsitution")
     */
    public function AboInstiResult(AbonnementInstitutionnel $abonnementInstitutionnel):JsonResponse{

        $content = json_decode($this->request->getContent());

            $abonnement = $abonnementInstitutionnel->retourAbonnementI($content->hab,$content->prof);


        $response = new JsonResponse();

        return $response->setData(['prix'=>$abonnement->getPrix(),"duree"=>$abonnement->getDuree()->format("%y année(s)"),'user'=>$abonnement->getUtlisateur(),"ttc"=>$abonnement->getPrix()*1.2]);
    }

    /**
     * @param AbonnementInstitutionnel $abonnementInstitutionnel
     * @return JsonResponse
     * @Route("/abonnementGc")
     */
    public function aboGcResult(AbonnementInstitutionnel $abonnementInstitutionnel):JsonResponse{
        $content = json_decode($this->request->getContent());

        $abonnement = $abonnementInstitutionnel->retourAbonnementGc(intval($content->utlisateur),$content->profil);
        $response = new JsonResponse();
        return $response->setData(['prix'=>$abonnement->getPrix(),'user'=>$abonnement->getUtlisateur(),"ttc"=>$abonnement->getPrix()*1.2]);
    }
}