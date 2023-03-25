<?php


namespace App\Controller;


use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\DefinirDate;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterventionOTD extends AbstractController
{
    use InterRepoTrait,SalarieRepoTrait,RequestTrait;

    /**
     * @param DefinirDate $definirDate
     * @return Response
     * @throws Exception
     * @Route ("/interventions réalisées",name="interRealise")
     */
    public function listeInter(DefinirDate $definirDate){
           $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
           $debut = $definirDate->debutDuMois();
           $fin = $definirDate->finDuMois();
           $intervention = $this->interventionRepository->findBySalarieTermine($salarie,$debut,$fin);

           return $this->render('salarie/interventionRealise.html.twig',[
               'interventions'=>$intervention
           ]);
    }

    /**
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @route("/findInter")
     */
    public function findInter(DefinirDate $definirDate):JsonResponse{
        $content = json_decode($this->request->getContent());
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $debut = DateTime::createFromFormat('j/m/Y H:i:s',$content->debut.' 00:00:01');
        $fin = DateTime::createFromFormat('j/m/Y H:i:s',$content->fin.' 23:59:59');
        $interventions = $this->interventionRepository->findBySalarieTermine($salarie,$debut,$fin);
        $liste =[];
        foreach ($interventions as $intervention){
            $liste[]=[
                'adresse'=>[
                    'numero'=>$intervention->getAdresse()->getNumero(),
                    'voie'=>$intervention->getAdresse()->getNomVoie(),
                    'cp'=>$intervention->getAdresse()->getCodePostal(),
                    'ville'=>$intervention->getAdresse()->getVille(),
                ],
                'date'=>$intervention->getRdvAT()->format('d/m/Y'),
                'type'=>$intervention->getTypeInter()->getNom(),
                'liste'=>$intervention->getListeInter()->getNom()
            ];
        }

        return new JsonResponse($liste);
    }
}