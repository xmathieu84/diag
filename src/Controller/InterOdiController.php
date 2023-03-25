<?php

namespace App\Controller;

use App\Repository\InterDiagRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterOdiController extends AbstractController
{
    public function __construct(private InterDiagRepository $interDiagRepository,
                                private RequestStack $requestStack,private EntityManagerInterface $manager)
    {
        $this->interDiagRepository=$interDiagRepository;
        $this->requestStack = $requestStack;
        $this->manager=$manager;
    }

    /**
     * @return Response
     * @Route("/inter-en-cours",name="interEnCoursOdi")
     */
    public function interEnCoursOdi():Response{
        $inters = $this->interDiagRepository->findBy(['odi'=>$this->getUser()->getSalarie(),"statut"=>"Intervention validée"],['dateRdv'=>"DESC"]);
        return $this->render('intervention/enterEncoursOdi.html.twig',[
            'inters'=>$inters
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/retrouverInterDate")
     */
    public function trouverInterDate():JsonResponse{
        $identifiant = $this->requestStack->getCurrentRequest()->getContent();
        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$identifiant]);
        $response = new JsonResponse();
        $interventions = $this->interDiagRepository->findInterForChoixDate($inter->getId(),$this->getUser()->getSalarie(),$inter->getDateRdv());
        $listeInter=[];
        foreach ($interventions as $intervention){
            $debut = \DateTime::createFromFormat("d/m/Y H:i:s",$intervention->getDateRdv()->format('d/m/Y').' '.$intervention->getHeureRdv()->format('H:i:s'));
            if ($intervention->getHeureRdv())
            {
                $heureFin = $intervention->getHeureRdv()->add($intervention->getDureeRdv());
                $finInter = \DateTime::createFromFormat("d/m/Y H:i:s",$intervention->getDateRdv()->format('d/m/Y').' '.$heureFin->format('H:i:s'));
                $fin = strtotime($finInter->format('d-m-Y H:i:s'));
                $finIntervention = $finInter->format('H:i:s');
            }
            else{
                $fin = "Pas de date de fin prévue";
                $finIntervention = $fin;
            }
            $listeInter[]=[
                'dateDebut'=>$debut->format('H:i'),
                'dateFin'=>$finIntervention,
                'debut'=>strtotime($debut->format('H:i')),
                'fin'=>$fin,
                'duree'=>$inter->getDureeRdv()->format('%i')
            ];
        }
        $response->setData(['duree'=>$inter->getDureeRdv()->format('%i'),'listeInter'=>$listeInter]);
        return $response;
    }

    /**
     * @return JsonResponse
     * @Route("/validerHeure")
     * @throws \JsonException
     */
    public function validerHeure():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $inter = $this->interDiagRepository->findOneBy(["identifiat"=>$content->id]);
        $date = (new DateTime())->setTimestamp($content->temps/1000);
        $inter->setHeureRdv($date);
        $this->manager->persist($inter);
        $this->manager->flush();
        return new JsonResponse();
    }
}