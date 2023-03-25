<?php

namespace App\Controller;


use App\Form\DesincrireType;
use App\Entity\Desinscription;

use App\Repository\FactureRepository;
use App\Repository\SalarieRepository;
use App\Repository\FacturesRepository;
use App\Repository\AnnulationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use App\Repository\InterventionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\IndisponibiliteRepository;
use App\Repository\PropositionRepository;
use App\Repository\TauxHoraireRepository;
use App\Service\DefinirDate;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DesinscriptionController extends AbstractController
{
    /**
     * @Route("/desinscription", name="desinscription")
     * @Security("is_granted('ROLE_ENTREPRISE') or is_granted('ROLE_DEMANDEUR')")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param SalarieRepository $salarieRepository
     * @param IndisponibiliteRepository $indisponibiliteRepository
     * @param ReservationRepository $reservationRepository
     * @param InterventionRepository $interventionRepository
     * @param PropositionRepository $propositionRepository
     * @param AnnulationRepository $annulationRepository
     * @param FacturesRepository $factureRepository
     * @param TauxHoraireRepository $tauxHoraireRepository
     * @param DefinirDate $definirDate
     * @return RedirectResponse|Response
     */
    public function seDesincrire(
        EntityManagerInterface $manager,
        Request $request,
        SalarieRepository $salarieRepository,
        IndisponibiliteRepository $indisponibiliteRepository,
        ReservationRepository $reservationRepository,
        InterventionRepository $interventionRepository,
        PropositionRepository $propositionRepository,
        AnnulationRepository $annulationRepository,
        FacturesRepository $factureRepository,
        TauxHoraireRepository $tauxHoraireRepository,
        DefinirDate $definirDate
    ) {
        $session = new Session();
        $user = $this->getUser();
        if ($user->getUserDem()) {
            $template = 'demandeur/basedemandeur.html.twig';
            $type = 'demandeur';
        } elseif ($user->getUserEnt() && $user->getUserEnt()->getFormJuridique() == 'auto-entrepreneur') {
            $template = 'entreprise/baseAE.html.twig';
            $type = 'entreprise auto-entrepreneur';
        } elseif ($user->getUserEnt() && $user->getUserEnt()->getFormJuridique() != 'auto-entrepreneur') {
            $template = 'entreprise/baseAE.html.twig';
            $type = 'entreprise ';
        }
        $depart = new Desinscription();
        $date = $definirDate->aujourdhui();
        $form = $this->createForm(DesincrireType::class, $depart);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['question']->getData() == 'oui') {
                $depart->setMail($user->getEmail());
            }
            if ($user->getUserEnt()) {

                $salaries = $salarieRepository->findBy(['entreprise' => $user->getUserEnt()]);

                foreach ($salaries as $salarie) {
                    $indisponiblites = $indisponibiliteRepository->findBy(['salarie' => $salaries]);
                    foreach ($indisponiblites as $indisponibilite) {
                        if ($indisponibilite) {
                            $salarie->removeIndisponibilite($indisponibilite);
                        }
                    }
                    $factures = $factureRepository->findBy(['salarie' => $salaries]);
                    foreach ($factures as $facture) {
                        $salarie->removeFacture($facture);
                    }
                    $propositions  = $propositionRepository->findBy(['salarie' => $salaries]);
                    foreach ($propositions as $proposition) {
                        if ($proposition) {
                            $salarie->removeProposition($proposition);
                        }
                    }

                    $reservations = $reservationRepository->findBy(['salarie' => $salaries]);
                    foreach ($reservations as $reservation) {
                        if ($reservation) {
                            $salarie->removeReservation($reservation);
                        }
                        $annulations = $annulationRepository->findBy(['salarie' => $salaries]);
                        foreach ($annulations as $annulation) {
                            if ($annulation) {
                                $salarie->removeAnnulation($annulation);
                            }
                        }
                    }
                    $tauxHs = $tauxHoraireRepository->findBy(['salarie' => $salarie]);
                    foreach ($tauxHs as $tauxH) {
                        if ($tauxH) {
                            $salarie->removeTauxHoraire($tauxH);
                        }
                    }
                }
                $manager->flush();



                foreach ($salaries as $salarie) {
                    if ($salarie) {

                        $manager->remove($salarie);
                    }
                }
                foreach ($user->getUserEnt()->getDrones() as $drone) {
                    $user->getUserEnt()->removeDrone($drone);
                }
                $manager->remove($user->getUserEnt());

                $manager->flush();
            }
            if ($user->getUserDem()) {
                $interventions = $interventionRepository->findBy(['intDem' => $user->getUserDem()]);
                foreach ($interventions as $intervention) {
                    if ($intervention) {
                        $user->getUserDem()->removeIntervention($intervention);
                        $facture = $factureRepository->findOneByIntervention($intervention);
                        $manager->remove($facture);
                    }
                }

                $manager->flush();
            }

            $session->invalidate();
            $depart->setType($type);
            $depart->setDate($date);
            $manager->persist($depart);
            $manager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('desinscription/seDesincrire.html.twig', [
            'template' => $template,
            'form' => $form->createView()
        ]);
    }
}
