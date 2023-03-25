<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\ReservationRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\AgentRepository;
use App\Repository\IndisponibiliteRepository;
use App\Repository\InterDiagRepository;
use App\Service\CreerPlanning;
use App\Service\Geoloc;
use PHPUnit\Util\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    use EntityManagerTrait,SalarieRepoTrait,DemandeurRepoTrait,RequestTrait,AgentRepoTrait,ReservationRepoTrait,InterRepoTrait;

    /**
     * @Route("/planningAgent/{id}", name="calendar")
     *
     * @param CreerPlanning $creerPlanning
     * @param int $id
     * @param AgentRepository $agentRepository
     * @return JsonResponse
     * @Security ("is_granted('ROLE_NIVEAU3')")
     */
    public function index( CreerPlanning $creerPlanning,int $id,AgentRepository $agentRepository): JsonResponse
    {
        $content =  json_decode($this->request->getContent());
        $creerPlanning->horaire($id,$agentRepository,$content);

        return  new JsonResponse($content);
    }



    /**
     * @param AgentRepository $agentRepository
     * @param int $id
     * @return JsonResponse
     * @Route ("/recupererPlanning/{id}")
     * @Security ("is_granted('ROLE_NIVEAU3')")
     */
    public function recuperePlanning(AgentRepository $agentRepository,int $id):JsonResponse{
        $agent =  $agentRepository->findOneBy(['id'=>$id]);
        $planning = $agent->getPlanning();
        $response = new JsonResponse();
         return $response->setData($planning->getHoraires());
    }



    /**
     * @param $id
     * @return JsonResponse
     * @Route("/calendarOTD/{id}")
     * @Security ("is_granted('ROLE_SALARIE')")
     */
    public function calendarOTD($id):JsonResponse{
        $salarie = $this->salarieRepository->findOneBy(['id'=>$id]);
        $reservations = $salarie->getReservations();
        $absences  =$salarie->getIndisponibilites();
        $calendrier = [];
        foreach ($reservations as $reservation) {
            if ($reservation->getDepart() && $reservation->getIntervention()){
                if ($reservation->getIntervention()->getIntDem()->getCivilite()){
                    $titre = ucfirst($reservation->getIntervention()->getListeInter()->getNom()) . ' '.$reservation->getIntervention()->getTypeInter()->getNom() .' '.$reservation->getIntervention()->getAdresse()->getVille().' '.$reservation->getIntervention()->getAdresse()->getCodePostal();
                }
                else{
                    $titre = 'intervention chez  ' . $reservation->getIntervention()->getIntDem()->getProfil() . ' ' . $reservation->getIntervention()->getIntDem()->getNom();
                }
                $calendrier[] = [
                    'start' => $reservation->getDebut()->format('Y-m-d\TH:i:sO'),
                    'end' => $reservation->getDepart()->format('Y-m-d\TH:i:sO'),
                    'title' => $titre,
                    'backgroundColor' => 'red',
                    'publicId'=>$reservation->getIntervention()->getId()
                ];
            }
        }

        foreach ($absences as $absence){
            $calendrier[] = [
                'start' => $absence->getDebut()->format('Y-m-d\TH:i:sO'),
                'end' => $absence->getFin()->format('Y-m-d\TH:i:sO'),
                'title' => $absence->getRaison(),
                'backgroundColor' => 'black',
                "publicId"=>$absence->getId(),
                "recurringDef"=>"absence"
            ];
        }
        return (new JsonResponse())->setData($calendrier);
    }

    /**
     * @param InterDiagRepository $interDiagRepository
     * @return JsonResponse
     * @Route("/calendarODI/{id}")
     */
    public function calendrierOdi(InterDiagRepository $interDiagRepository,int $id):JsonResponse{
        $salarie = $this->getUser()->getSalarie();
        $inters = $interDiagRepository->findBy(['odi'=>$salarie,'statut'=>'Intervention validée']);

        $calendar = [];
        foreach ($inters as $inter){
            if ($inter->getHeureRdv()){
                $debut = \DateTimeImmutable::createFromFormat("d/m/Y H:i",$inter->getDateRdv()->format('d/m/Y')." ".$inter->getHeureRdv()->format("H:i"));
                $fin = $debut->add($inter->getDureeRdv());
                $calendar[]=[
                    'start' => $debut->format('Y-m-d\TH:i:sO'),
                    'end' => $fin->format('Y-m-d\TH:i:sO'),
                    'title' => "test",
                    'backgroundColor' => 'red',
                    'publicId'=>$inter->getId()
                ];
            }
        }
        return new JsonResponse($calendar);
    }
    /**
     * @return JsonResponse
     * @Route("/recuperePlanningAllAgent")
     */
    public function recuperePlanningAllAgent():JsonResponse{
        $institution = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $listePlanning=[];
        $response = new JsonResponse();
        foreach ($institution->getResponsable() as $responsable){
            if ($responsable->getPlanning()){
                foreach ($responsable->getPlanning()->getHoraires() as $horaire){
                    $listePlanning[] = $horaire;
                }


            }
            foreach ($responsable->getChef() as $employe){
                if ($employe->getPlanning()){
                    foreach ($employe->getPlanning()->getHoraires() as $horaire){
                        $listePlanning[] = $horaire;
                    }

                }

            }
        }
        foreach ($institution->getChef() as $employe){
            if ($employe->getPlanning()){
                foreach ($employe->getPlanning()->getHoraires() as $horaire){
                    $listePlanning[] = $horaire;
                }

            }
        }
        return $response->setData($listePlanning);

 }

    /**
     * @return Response
     * @Route("/planningOtd",name="planningOtd")
     * @Security ("is_granted('ROLE_ENTREPRISE') and !is_granted('ROLE_AE')")
     */
    public function planningOtd():Response{
        return $this->render('entreprise/planningOtd.tml.twig');
    }

    /**
     * @param IndisponibiliteRepository $repository
     * @return JsonResponse
     * @Route("/planningAllOtd")
     */
    public function planningAllOtd(IndisponibiliteRepository $repository):JsonResponse{
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $reservations = $this->reservationRepository->findByEntreprise($salarie->getEntreprise());
        $calendrier=[];
        $indispos = $repository->findByEntreprise($salarie->getEntreprise());
        foreach ($reservations as $reservation){
            if ($reservation->getDebut() && $reservation->getDepart()){
                $calendrier[]=  [
                    'start' => $reservation->getDebut()->format('Y-m-d\TH:i:sO'),
                    'end' => $reservation->getDepart()->format('Y-m-d\TH:i:sO'),
                    'title' => 'intervention chez ' . $reservation->getIntervention()->getIntDem()->getCivilite()->getPrenom() . ' ' . $reservation->getIntervention()->getIntDem()->getCivilite()->getNom(),
                    'backgroundColor' => 'red'
                ];
            }
        }
        foreach ($indispos as $indispo){
            $calendrier[]=  [
                'start' => $indispo->getDebut()->format('Y-m-d\TH:i:sO'),
                'end' => $indispo->getFin()->format('Y-m-d\TH:i:sO'),
                'title' => $indispo->getRaison().' '.$indispo->getSalarie()->getCivilite()->getPrenom().' '.$indispo->getSalarie()->getCivilite()->getNom(),
                'backgroundColor' => 'blue'
            ];
        }
        return new JsonResponse($calendrier);

    }

    /**
     * @param IndisponibiliteRepository $repository
     * @param InterDiagRepository $interDiagRepository
     * @return JsonResponse
     * @Route("/planningAllOdi")
     */
    public function planningAllOdi(IndisponibiliteRepository $repository,InterDiagRepository $interDiagRepository):JsonResponse{
        $indispos = $repository->findByEntreprise($this->getUser()->getSalarie()->getEntreprise());
        $inters = $interDiagRepository->findByEntreprise($this->getUser()->getSalarie()->getEntreprise());
        $calendrier=[];
        foreach ($inters as $inter){

            if ($inter->getDemandeur()->getUser()){
                $titre = "Intervention chez ".$inter->getDemandeur()->getCivilite()->getPrenom(). ' '.$inter->getDemandeur()->getCivilite()->getNom();
            }else{
                $titre = "Intervention chez ".$inter->getDemandeur()->getProfil(). ' '.$inter->getDemandeur()->getNom();
            }

            if ($inter->getHeureRdv()){
                $debut = \DateTimeImmutable::createFromFormat('d/m/Y H:i',$inter->getDateRdv()->format('d/m/Y').' '.$inter->getHeureRdv()->format('H:i'));
                $fin = $debut->add($inter->getDureeRdv());

                $calendrier[]=  [
                    'start' => $debut->format('Y-m-d\TH:i:sO'),
                    'end' => $fin->format('Y-m-d\TH:i:sO'),
                    'title' => $titre,
                    'backgroundColor' => 'red'
                ];
            }
        }
        return new JsonResponse($calendrier);
    }

    /**
     * @param Geoloc $geoloc
     * @return JsonResponse
     * @Route ("/recupereInterCalendar")
     */
    public function recupereInterCalendar(Geoloc $geoloc):JsonResponse{
        $id = $this->request->getContent();
        $intervention = $this->interventionRepository->findOneBy(['id'=>$id]);
        $liste = [];
        if ($intervention->getIntDem()->getCivilite()){
            $nomDemandeur = $intervention->getIntDem()->getCivilite()->getPrenom().' '.$intervention->getIntDem()->getCivilite()->getNom();
        }
        else{
            $nomDemandeur = $intervention->getIntDem()->getProfil().' '.$intervention->getIntDem()->getNom();
        }
        $temps = $intervention->getRdvAT()->diff($intervention->getReservation()->getDebut());
       $distance = $geoloc->coutKilometre($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),$intervention->getPropositionChoisie()->getSalarie()->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getPropositionChoisie()->getSalarie()->getAdresse()->getCoordonnees()->getLongitude());

        $liste[]= [
            'type'=>$intervention->getListeInter()->getNom().' '.$intervention->getTypeInter()->getNom(),
            'adresse'=>$intervention->getAdresse()->getNumero().' '.$intervention->getAdresse()->getNomVoie().' '.$intervention->getAdresse()->getCodePostal().' '.$intervention->getAdresse()->getVille(),
            'demandeur'=>$nomDemandeur,
            'temps'=>$temps->format('%h').' h '.$temps->format('%i').' min',
            'distance'=>$distance/1000 .' km',
            'drone'=>$intervention->getDrone()->getNomFabriquant().' '.$intervention->getDrone()->getTypeDrone(),
            'rdv'=>$intervention->getRdvAT()->format('d/m/Y').' '.$intervention->getRdvAT()->format('H:i'),
            'detail'=>$intervention->getInterPrecision()



        ];
        return new JsonResponse($liste);

    }

    /**
     * @param InterDiagRepository $interDiagRepository
     * @return JsonResponse
     * @Route("/infoInterOdi")
     */
    public function infoInterOdi(InterDiagRepository $interDiagRepository,Geoloc $geoloc):JsonResponse{
        $inter = $interDiagRepository->find($this->request->getContent());
       $info = [];
        $listeMission=null;
        if ($inter->getDemandeur()->getCivilite()){
            $nomDemandeur = $inter->getDemandeur()->getCivilite()->getPrenom().' '.$inter->getDemandeur()->getCivilite()->getNom();
        }
        else{
            $nomDemandeur = $inter->getDemandeur()->getProfil().' '.$inter->getDemandeur()->getNom();
        }
        foreach ($inter->getMissions() as $mission){
            $listeMission .= $mission->getNom().'<br>';
        }
        $distance = $geoloc->coutKilometre($inter->getAdresse()->getCoordonnees()->getLatitude(),$inter->getAdresse()->getCoordonnees()->getLongitude(),$inter->getOdi()->getAdresse()->getCoordonnees()->getLatitude(),$inter->getOdi()->getAdresse()->getCoordonnees()->getLongitude());
        $heure = number_format($inter->getDureeRdv()->format("%i")/60,0);
        $minute = $inter->getDureeRdv()->format('%i')%60;
        $liste[]=[
            'type'=>$listeMission,
            'adresse'=>$inter->getAdresse()->adresseComplete(),
            'demandeur'=>$nomDemandeur,
            'temps'=>$heure.'h'.$minute,
            'distance'=>$distance/1000 .' km',
            'drone'=>' ',
            'rdv'=>$inter->getDateRdv()->format('d/m/Y').' à '.$inter->getHeureRdv()->format('H:i'),
            'detail'=>" "
        ];
        return new JsonResponse($liste);
    }

    /**
     * @param int $id
     * @param IndisponibiliteRepository $repository
     * @return JsonResponse
     * @Route ("/SuppimerAbsence/{id}")
     * @isGranted ("ROLE_SALARIE")
     */
    public function SuppimerAbsence(int $id,IndisponibiliteRepository $repository):JsonResponse{
        $indispo = $repository->findOneBy(['id'=>$id]);
        $this->manager->remove($indispo);
        $this->manager->flush();
        return new JsonResponse();
    }
}
