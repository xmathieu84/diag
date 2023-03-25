<?php

namespace App\Controller;

use App\Helper\EntrepriseRepoTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\ReservationRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\FacturesRepository;
use DateTime;
use App\Repository\PourcentageRepository;
use App\Service\choixTemplate;
use App\Service\DefinirAcces;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class GestionController extends AbstractController
{
    use ReservationRepoTrait, RequestTrait, SalarieRepoTrait,EntrepriseRepoTrait,InterRepoTrait;

    /**
     * @Route("/entreprise/factures",name="factures")
     * @isGranted("ROLE_ENTREPRISE")
     */
    public function Factures(choixTemplate $choixTemplate, DefinirAcces $definirAcces)
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();


        $template = $choixTemplate->templateAE($entreprise);
        return $this->render('entreprise/facture.html.twig', [
            'template' => $template,
            'entreprise' => $entreprise,

        ]);
    }
    /**
     * @Route("/entreprise/recuperer",name="recupere")
     * @isGranted("ROLE_ENTREPRISE")
     * 
     */
    public function recuperer(FacturesRepository $facturesRepository)
    {
        $periode = $this->request->getContent();
        $Periode = json_decode($periode, true);
        $fact = [];
        $debut = $Periode['debut'];
        $fin = $Periode['fin'];
        $Debut = DateTime::createFromFormat('!d/m/Y', $debut);
        $Fin = DateTime::createFromFormat('!d/m/Y', $fin);
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $factures = $facturesRepository->findByDateEntreprise($entreprise, $Debut, $Fin);
        $interventions = $this->interventionRepository->findByEntreprise($entreprise, $Debut, $Fin);
        foreach ($factures as $facture) {
            $fact[] = [
                'lien' => '/uplaods/factureEnt/',
                'nom' => $facture->getNom()
            ];

        }

        foreach ($interventions as $intervention){
            $fact[] = [
                'lien' => '/uplaods/factureDD/',
                'nom' => $intervention->getDevis()
            ];
            $fact[] = [
                'lien' => '/uploads/factureDD/',
                'nom' => $intervention->getFacture()
            ];

        }

        $reponse = new JsonResponse();


        return $reponse->setData($fact);
    }
}
