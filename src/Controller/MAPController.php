<?php

namespace App\Controller;

use App\Entity\MAP;

use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Service\choixTemplate;
use App\Repository\MAPRepository;
use App\Service\DefinirDate;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MAPController extends AbstractController
{
    use InterRepoTrait, RequestTrait, EntityManagerTrait,InterRepoTrait;
    /**
     * @Route("/map", name="map")
     *  @isGranted("ROLE_SALARIE")
     *
     * @param MAPRepository $mapRepository
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     */
    public function listeMap(MAPRepository $mapRepository, choixTemplate $choixTemplate, DefinirDate $definirDate): Response
    {
        $choix = $choixTemplate->templateSal($this->getUser());
        $salarie = $this->getUser()->getSalarie();

        $debut = $definirDate->DebutAnnee();
        $fin = $definirDate->FinDannee();
        $interventions = $this->interventionRepository->findBySalarieTermine($salarie, $debut, $fin);
        $debutTemps = new DateTime();
        $debutTemps->setTimestamp(0);
        $finTemps = new DateTime();
        $finTemps->setTimestamp(0);
        $duree = null;
        foreach ($interventions as $intervention) {

            if($intervention->getMAP()){
                if (!$intervention->getMAP()->getDureeVol()){
                    $vol = new DateInterval('P0Y0M0DT99H');
                }
                else{
                    $vol = $intervention->getMAP()->getDureeVol();
                }

                $duree = $finTemps->add($vol);

            }
        }
        if ($interventions && $duree) {
            $tempsFormatte = date_diff($duree, $debutTemps);
            $total = 24 * $tempsFormatte->format('%d') + $tempsFormatte->format('%h') . ' h ' . $tempsFormatte->format('%i');
        } else {
            $total = 0;
        }



        return $this->render('salarie/map.html.twig', [
            'template' => $choix[1],
            'interventions' => $interventions,
            'temps' => $total,
            'otd' => $salarie

        ]);
    }
    /**
     * @Route("/remplirManex",name="remplirMap")
     * @isGranted("ROLE_SALARIE")
     * 
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function remplirMap(choixTemplate $choixTemplate): Response
    {
        $choix = $choixTemplate->templateSal($this->getUser());
        $salarie = $choix[0];
        $interventions = $this->interventionRepository->interSansMap($salarie->getId());

        return $this->render('salarie/remplirMap.html.twig', [
            'template' => $choix[1],
            'interventions' => $interventions

        ]);
    }

    /**
     * @Route("/validerMap")
     * @isGranted("ROLE_SALARIE")
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function validerMap(): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $intervention = $this->interventionRepository->findOneBy(['id' => $contenu['idInter']]);
        $interval = 'PT' . $contenu['heure'] . 'H' . $contenu['minute'] . 'M';
        $map = new MAP();
        $duree = new DateInterval($interval);
        $map->setObservation($contenu['observation'])
            ->setDureeVol($duree);
        $intervention->setMAP($map);
        $this->manager->persist($intervention);
        $this->manager->persist($map);
        $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse;
    }
}
