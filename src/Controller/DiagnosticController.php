<?php

namespace App\Controller;

use App\Repository\InterDiagRepository;
use App\Repository\MissionRepository;
use App\Repository\PackOdiPrixTailleRepository;
use App\Repository\PourcentageRepository;
use App\Repository\PrixOdiMissionRepository;
use App\Repository\RemiseExceptionRepository;
use App\Repository\SalarieRepository;
use App\Repository\SignatureAdminRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\FacturePdfService;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiagnosticController extends AbstractController
{
    public function __construct(private InterDiagRepository $interDiagRepository,
                                private RequestStack $requestStack,private EntityManagerInterface $manager,
                                private PrixOdiMissionRepository $prixOdiMissionRepository,
                                private choixTemplate $choixTemplate,
                                private MissionRepository $missionRepository,private SalarieRepository $salarieRepository){}

    /**
     * @param int $id
     * @return Response
     * @Route("/resultatInter/{id}",name="resultatInter")
     *
     */
    public function resulatInter(string $id,SignatureAdminRepository $repository){
        $template = $this->choixTemplate->templateDiag($this->getUser());
        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$id]);
        if ($inter->getTailleBien()->getTypeBien()->getNom()==="Appartement"){
            if ($inter->getTypeDiag()==="vente"){
                $missions = $this->missionRepository->findBy(['venteAppart'=>true,"actif"=>true]);
            }
            else{
                $missions = $this->missionRepository->findBy(['locationAppart'=>true,"actif"=>true]);
            }
        }elseif ($inter->getTailleBien()->getTypeBien()->getNom()==="Maison"){
            if ($inter->getTypeDiag()==="vente"){
                $missions = $this->missionRepository->findBy(['venteMaison'=>true,"actif"=>true]);
            }
            else{
                $missions = $this->missionRepository->findBy(['locationMaison'=>true,"actif"=>true]);
            }
        }
        dump($inter);
        return $this->render('intervention/resulatInterDiag.html.twig',[
            'template'=>$template,
            "missions"=>$missions,
            'inter'=>$inter
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/prixMissions/{id}")
     * @throws JsonException
     *
     */
    public function prixMissionInter(int $id,SalarieRepository $salarieRepository):JsonResponse{
        $inter = $this->interDiagRepository->find($id);
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $missions = $this->missionRepository->findMissions($content->mission);
        //$salaries = $salarieRepository->findByMission($missions,$inter);
        dump($content);
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws JsonException
     * @Route("/ajoutMissionInter/{id}")
     *
     */
    public function ajoutMissionInter(int $id):JsonResponse{
        $inter = $this->interDiagRepository->find($id);
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $missions = $this->missionRepository->findMissions($content->liste);
        foreach ($missions as $mission){
            $inter->addMission($mission);
        }
        $this->manager->persist($mission);
        $this->manager->flush();
         return new JsonResponse($id);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/choixOdi/{id}",name="choixOdi")
     *
     */
    public function choixOdi(int $id,):Response{
        $inter = $this->interDiagRepository->find($id);
        $template = $this->choixTemplate->templateDiag($this->getUser());
        return $this->render("intervention/choixOdi.html.twig",[
            'inter'=>$inter,
            'date'=>new \DateTime('NOW'),
            'missions'=>$inter->getMissions()->toArray(),
            'template'=>$template
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/infoCaledrierOdi/{id}")
     * @throws JsonException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     */
    public function infoCaledrierOdi(DefinirDate $definirDate,$id,RemiseExceptionRepository $remiseExceptionRepository,PackOdiPrixTailleRepository $packOdiPrixTailleRepository):JsonResponse{

        $background=['blue',"red","green"];
        $jour = $definirDate->aujourdhuiImmutable();
        $demain = $jour->add(new \DateInterval('P1D'));
        $debut = \DateTimeImmutable::createFromFormat('d/m/Y',date('d/m/Y',$this->requestStack->getCurrentRequest()->query->get('start')/1000));
        $nouveauPrix=[];
        if ($demain>$debut){
            $dateDebut = $demain;
        }
        else{
            $dateDebut = $debut;
        }
        $fin = \DateTime::createFromFormat('d/m/Y',date('d/m/Y',$this->requestStack->getCurrentRequest()->query->get('end')/1000));

       //Trouver tarif pour chaque jour et chaque ODI
        $inter = $this->interDiagRepository->find($id);
        $salaries = $this->salarieRepository->findForMission($inter);

        $missions = $inter->getMissions()->toArray();
        $delai = date_diff($fin,$dateDebut,false)->days;
        $listeFinale = [];
        $listeOdi = [];
        $listePack =[];
        foreach ($salaries as $salary){
            $missionSalarie = $this->missionRepository->findBySalarie($salary);
            $conpatible = true;
            foreach ($missions as $mission){

                if (!in_array($mission, $missionSalarie, true)){
                    $conpatible = false;
                }
            }
            if ($conpatible){
                $listeOdi[]=$salary;
                $packPrixs = $packOdiPrixTailleRepository->findForInter($inter->getTailleBien(),$salary);

                foreach ($packPrixs as $packPrix)
                {
                    if (empty(array_diff($missions,$packPrix->getPackOdi()->getPack()->getMissions()->toArray())))
                    {
                        $listePack[]=$packPrix;
                    }
                }

            }


        }

        for ($i=0;$i<=$delai;$i++) {
            $dateInter = \DateTime::createFromImmutable($dateDebut->add(new \DateInterval("P" . $i . "D")));
            $liste = [];
            $j = 0;
            foreach ($listeOdi as $salary) {
                $prix = $this->prixOdiMissionRepository->findForInterMission($salary, $inter->getMissions()->toArray(), $inter->getTailleBien());
                $inters = $this->interDiagRepository->findBy(['odi' => $salary, 'dateRdv' => $dateInter]);
                $total = 0;
                $duree = 0;
                $prixPack = 0;
                foreach ($prix as $item) {
                    $remise = $remiseExceptionRepository->findForInter($dateInter,$item);
                    if ($remise){
                        $prixFinal = $item->getPrix()/(1+$remise->getTaux()/100);
                    }
                    else{
                        $prixFinal= $item->getPrix();
                    }
                    $total += $prixFinal;
                    $duree += $item->getTemps();
                }

                if (!empty($listePack)){
                    foreach ($listePack as $pack){

                        if ($pack->getPackOdi()->getOdi()->getId()===$salary->getId()){
                            $remisePack = $remiseExceptionRepository->findForPackInter($dateInter,$pack);
                            if ($remisePack){
                                $prixPack = $pack->getPrix()/(1+$remisePack->getTaux()/100);
                            }
                            else{
                                $prixPack = $pack->getPrix();
                            }
                        }
                    }


                }
                if ($prixPack!==0&&$total>$prixPack){
                    $nouveauPrix = ['total'=>$prixPack,'pack'=>$pack->getId()];
                }
                else{
                    $nouveauPrix = ['total'=>$total,'pack'=>null];
                }

                if (!empty($inters)){
                    $totalInter = 0;
                    foreach ($inters as $inter){
                        $totalInter += (int)$inter->getDureeRdv()->format('%i');
                    }
                    if ($duree + $totalInter < 12*60 ){
                        $liste[] = [
                            'total' => number_format($nouveauPrix['total'], 2) . " €",
                            'duree' => intdiv($duree, 60) . " h " . $duree % 60,
                            'salarie' => $salary->getId(),
                            'couleur' => $background[$j],
                            'dureeS' => $duree,
                            "prix"=>number_format($nouveauPrix['total'],2),
                            "pack"=>$nouveauPrix['pack']

                        ];
                    }
                }else{
                    $liste[] = [
                        'total' => number_format($nouveauPrix['total'], 2) . " €",
                        'duree' => intdiv($duree, 60) . " h " . $duree % 60,
                        'salarie' => $salary->getId(),
                        'couleur' => $background[$j],
                        'dureeS' => $duree,
                        "prix"=>number_format($nouveauPrix['total'],2),
                        "pack"=>$nouveauPrix['pack']
                    ];
                }


                $j++;
            }
            $trie = array_column($liste, 'total');

            array_multisort($trie, 3, $liste);
            if (count($liste) > 3) {
                while (count($liste) > 3) {
                    array_pop($liste);
                }
            }
            foreach ($liste as $item) {
                $listeFinale[] = [
                    'date' => $dateInter->format('Y-m-d\TH:i:s'),
                    'salarie' => $item['salarie'],
                    'backgroud' => $item['couleur'],
                    'total' => $item['total'],
                    'prix'=> $item['prix'],
                    'duree' => $item['duree'],
                    'dureeS' => $item['dureeS'],
                    'pack'=>$item['pack']
                ];
            }
        }
        return new JsonResponse($listeFinale);
    }

    /**
     * @param PourcentageRepository $pourcentageRepository
     * @param string|null $type
     * @return JsonResponse
     * @throws JsonException
     * @Route("/dispoOdi/{type}")
     *
     */
    public function dispoOdi(PourcentageRepository $pourcentageRepository,string $type=null):JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $date = \DateTime::createFromFormat('d/m/Y',$content->date);
        $salarie = $this->salarieRepository->find($content->salarie);
        $retour = new stdClass();
        if ($salarie->getRemiseTemps()){
            if ($salarie->getRemiseTemps()->getJournee()){
                if (!$type){
                    $taux = $pourcentageRepository->findOneBy(['nom'=>'journee']);
                }
                else{
                    $taux = $pourcentageRepository->findOneBy(['nom'=>'demi journee']);
                }

                $retour->journee = 1+$taux->getTaux()/100;
                $retour->remise = $taux->getTaux();

            }
        }
        $retour->date=$date->format('d/m/Y');

        return new JsonResponse($retour);
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/choixMoment")
     *
     */
    public function choixMoment():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $salarie = $this->salarieRepository->find($content->odi);
        $dateInter = \DateTime::createFromFormat('d/m/Y',$content->date);
        $dureeInter = new \DateInterval('PT'.$content->duree.'M');
        $inters = $this->interDiagRepository->findBy(['odi'=>$salarie,'dateRdv'=>$dateInter]);
        if ($content->moment ==="matin"){
            $dateMin = \DateTime::createFromFormat('d/m/Y H:m',$content->date." 8:00");
            $dateMax = \DateTime::createFromFormat('d/m/Y H:m',$content->date." 14:00");
        }
        else{
            $dateMin = \DateTime::createFromFormat('d/m/Y H:m',$content->date." 14:00");
            $dateMax = \DateTime::createFromFormat('d/m/Y H:m',$content->date." 20:00");
        }
       if (!empty($inters)){
           $totalInter = 0;
           foreach ($inters as $inter){
               $dateInterOdi = \DateTime::createFromFormat("d/m/Y H:m",$inter->getDateRdv()->format('d/m/Y').' '.$inter->getHeureRdv()->format('H:m'));
               if ($dateInterOdi>$dateMin&&$dateInter<=$dateMax){
                    $totalInter +=(int)$inter->getDureeRdv()->format("%i");
               }
               if ($totalInter+(int)$dureeInter->format("%i")>6*60){
                   $dispo = 'indisponible';
               }
               else{
                   $dispo = 'disponible';
               }
           }
       }
       else{
           $dispo = "disponible";
       }

        return new JsonResponse($dispo);
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/choixHeure")
     *
     */
    public function choixHeure():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $salarie = $this->salarieRepository->find($content->odi);
        $dateInter = \DateTimeImmutable::createFromFormat('d/m/Y H:m',$content->date." ".$content->heure);
        $dureeInter = new \DateInterval('PT'.$content->duree.'M');
        $inters = $this->interDiagRepository->findBy(['odi'=>$salarie,'dateRdv'=>$dateInter]);
        $finInter = $dateInter->add($dureeInter);
        $dispo = true;
        if (!empty($inters)){
            foreach ($inters as$inter){
                $dateInterOdi = \DateTime::createFromFormat("d/m/Y H:m",$inter->getDateRdv()->format('d/m/Y').' '.$inter->getHeureRdv()->format('H:m'));
                if ($dateInterOdi<$finInter){
                    $dispo = false;
                    break;
                }
            }
        }
        if ($dispo){
            return new JsonResponse("disponible");
        }
        else{
            return new JsonResponse("indisponible");
        }

    }

    /**
     * @return JsonResponse
     * @Route("/validerDiag")
     * @throws JsonException
     */
    public function validerDiag(PackOdiPrixTailleRepository $packOdiPrixTailleRepository,
                                PourcentageRepository $pourcentageRepository,FacturePdfService $facturePdfService):JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $salarie = $this->salarieRepository->find($content->odi);
        $inter = $this->interDiagRepository->find($content->intervention);
        $dateInter = \DateTimeImmutable::createFromFormat('d/m/Y',$content->date);
        $dureeInter = new \DateInterval('PT'.$content->duree.'M');
        $inscrit = false;
        $inter->setOdi($salarie);
        $taux = $pourcentageRepository->findOneBy(['nom'=>"acompte"]);
        if ($content->pack){
            $pack = $packOdiPrixTailleRepository->find($content->pack);
            $inter->setPack($pack);
        }
        if ($content->moment ==="Heure précise")
        {
            $heureRdv = \DateTime::createFromFormat('H:m',$content->rdv);
            $inter->setHeureRdv($heureRdv)
                ->setRemiseTemps(0);
        }
        $inter->setDureeRdv($dureeInter)
            ->setDateRdv($dateInter)
            ->setPrix($content->prix)
            ->setMoment($content->moment);
        if ($salarie->getRemiseTemps()){
            if ($content->moment==="Dans la journee" && $salarie->getRemiseTemps()->getJournee()){
                $tauxRemise = $pourcentageRepository->findOneBy(['nom'=>"journee"]);
                $inter->setRemiseTemps($tauxRemise->getTaux());
            }
            elseif ($salarie->getRemiseTemps()->getDemiJournee()){
                $tauxRemise = $pourcentageRepository->findOneBy(['nom'=>"demi journee"]);
                $inter->setRemiseTemps($tauxRemise->getTaux());
            }
        }
        if ($this->getUser()){

            if ($this->getUser()->hasRole('ROLE_DEMANDEUR')){
                $inter->setAcompte($inter->getPrix()*($taux->getTaux()/100));
                $factureAcompte = $facturePdfService->createFactureDiag($inter,'acompte');
                $factureInter = $facturePdfService->createFactureDiag($inter,'inter');
                $inter->setFactureAcompte($factureAcompte)
                    ->setStatut("en attente de paiement")
                    ->setAcompte($inter->getPrix()*($taux->getTaux()/100))
                    ->setFacture($factureInter);
                $inscrit = "demandeur";
        }else{
                $factureInter = $facturePdfService->createFactureDiag($inter,'inter');
                $inter->setFacture($factureInter)
                        ->setStatut("Intervention validée");
                $inscrit = "gc";
            }
        }


        $this->manager->persist($inter);
        $this->manager->flush();
        return new JsonResponse([
            'inscrit'=>$inscrit,
            'inter'=>$content->intervention
        ]);
    }
}