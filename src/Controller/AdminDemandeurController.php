<?php

namespace App\Controller;

use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\ReservationRepoTrait;
use App\Repository\DemandeurRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AdminDemandeurController
 * @package App\Controller
 */
class AdminDemandeurController extends AbstractController
{
    use EntityManagerTrait, RequestTrait, InterRepoTrait, ReservationRepoTrait,DemandeurRepoTrait;
    /**
     * @Route("/administrateur/demandeur", name="admindemandeur")
     */
    public function index()
    {

        $demandeurs = $this->demandeurRepository->findAll();

        return $this->render('administrateur/demandeur.html.twig', [
            'demandeurs' => $demandeurs
        ]);
    }


    /**
     * @Route("/administrateur/demandeur/intervention/{id}", name="interventionDemandeur")
     * @param DemandeurRepository $demandeurRepo
     * @param $id
     * @return Response
     */
    public function interventionDemandeur(DemandeurRepository $demandeurRepo, $id)
    {

        $demandeur = $demandeurRepo->findOneBy(['id' => $id]);
        $reservations = [];
        $interventions = $this->interventionRepository->findBy(['intDem' => $demandeur]);
        foreach ($interventions as $intervention) {
            $reservation = $this->reservationRepository->findOneBy(['intervention' => $intervention]);
            $reservations[] = $reservation;
        }


        return $this->render('administrateur/interdemandeur.html.twig', [
            'demandeur' => $demandeur,
            'reservations' => $reservations
        ]);
    }
}
