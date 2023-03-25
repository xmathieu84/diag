<?php

namespace App\Controller;

use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\FactureInstiTrait;
use App\Helper\RequestTrait;

use App\Helper\SalarieRepoTrait;
use App\Repository\FactureOtdRepository;
use App\Service\DefinirDate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComptableController extends AbstractController
{
    use EntrepriseRepoTrait,DemandeurRepoTrait,RequestTrait,SalarieRepoTrait,AgentRepoTrait,FactureInstiTrait;
    /**
     * @return Response
     * @Route ("/administrateur/listeMandatPrélèvement",name="listeMandatPrelevement")
     */
    public function listeMandatPrelevement():Response{

        $entreprise = $this->entrepriseRepository->findAll();
        $grandCompte = $this->demandeurRepository->findInstitution();
        return $this->render("administrateur/listeMandat.html.twig",[
            'entreprises'=>$entreprise,
            'grandcomptes'=>$grandCompte
        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/administrateur/rechercheMandat")
     */
        public function compteParMail():JsonResponse{
            $content = json_decode($this->request->getContent());
            $salaries = $this->salarieRepository->findDirigeantByMail($content->email);
            $liste = [];
            $banquemandat =[];

            $mandat=[];
            if (count($salaries)>0){
                foreach ($salaries as $salary){
                    $preleve=

                          $salary->getEntreprise()->getDenomination().' '.$salary->getEntreprise()->getFormJuridique()

                    ;
                    foreach ($salary->getEntreprise()->getBanques() as  $banque){
                        $banquemandat[]=[

                                'nom'=>$banque->getNom(),


                        ];
                    }foreach ($salary->getEntreprise()->getBanques() as  $banque){
                        $mandat[]=[
                                'mandat'=>$banque->getSepaSigne(),
                        ];
                    }
                    $liste[]=[
                        'banque'=>$banquemandat,
                        'preleve'=>$preleve,
                        'mandat'=>$mandat
                    ];
                }
            }

            return new JsonResponse($liste);
        }

    /**
     * @param DefinirDate $definirDate
     * @param FactureOtdRepository $repository
     * @return Response
     * @throws \Exception
     * @Route("/administrateur/facturePresta",name="facturePresta")
     */
   public function facturePrestaDD(DefinirDate $definirDate,FactureOtdRepository $repository):Response{
            $debut = $definirDate->debutDuMois();
            $fin = $definirDate->finDuMois();
            $facturesOtd = $repository->findByMonth($debut,$fin);
            $factureinsti = $this->factureInstiRepository->findByRole($debut,$fin,'ROLE_INSTITUTION');
            $factureGc =  $this->factureInstiRepository->findByRole($debut,$fin,'ROLE_GRANDCOMPTE');

            return  $this->render("administrateur/facturePresta.html.twig",[
                'factures'=>$facturesOtd,
                'factureIs'=>$factureinsti,
                'factureGc'=>$factureGc
            ]);
   }

    /**
     * @param FactureOtdRepository $repository
     * @return JsonResponse
     * @route("/administrateur/retrouverFacturePresta")
     */
   public function retrouverFacture(FactureOtdRepository $repository):JsonResponse{
       $content = json_decode($this->request->getContent());
       $nom = [];
       $debut = \DateTime::createFromFormat('d/m/Y H:i:s',$content->premier." 00:00:00");
       $fin = \DateTime::createFromFormat('d/m/Y H:i:s',$content->dernier." 23:59:59");

      if ($content->type =="gc"){
           $factures = $this->factureInstiRepository->findByRole($debut,$fin,'ROLE_GRANDCOMPTE');

            $lien = "/uploads/factureInsti/";
       }
       elseif ($content->type =='insti'){
           $factures = $this->factureInstiRepository->findByRole($debut,$fin,'ROLE_INSTITUTION');
           $lien = "/uploads/factureInsti/";
       }
       else{
           $factures = $repository->findByMonth($debut,$fin);
           $lien = "/uploads/factureDD/";
       }
       foreach ($factures as $facture){
           $nom[]=[
               'nom'=>$facture->getNom(),
               'lien'=>$lien
           ];
       }

       return new JsonResponse($nom);
   }
}