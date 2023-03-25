<?php

namespace App\Controller;

use App\Helper\DroneRepoTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Repository\MandatCerfaRepository;
use App\Service\SmsFactor;
use DateTime;

use App\Service\Mail;
use App\Service\Geoloc;
use App\Form\DepartType;
use App\Form\IndispoType;
use App\Entity\Annulation;
use App\Service\InterRepo;
use App\Form\AnnulationType;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use App\Service\DefinirAcces;
use App\Service\DefinirObjet;
use App\Helper\InterRepoTrait;
use App\Helper\PaginatorTrait;
use App\Entity\Indisponibilite;
use App\Helper\SalarieRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\PropositionRepoTrait;
use App\Helper\ReservationRepoTrait;
use App\Service\choixTemplate;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\Json;

class SalarieController extends AbstractController
{
    use SalarieRepoTrait,
        ReservationRepoTrait,
        EntityManagerTrait,
        RequestTrait,
        EntrepriseRepoTrait,
        etatAboRepoTrait,
        InterRepoTrait,
        ReservationRepoTrait,
        PropositionRepoTrait,
        PaginatorTrait,DroneRepoTrait;


    /**
     * @Route("/demcours/{idsalarie}", name="demcours")
     * @Security("is_granted('ROLE_SALARIE') and is_granted('ROLE_PREMIUM') or is_granted('ROLE_FREE')")
     * @param int|null $idsalarie
     * @param DefinirDate $definirDate
     * @param DefinirAcces $definirAcces
     * @return Response
     * @throws Exception
     */
    public function demcours( DefinirDate $definirDate, DefinirAcces $definirAcces,int $idsalarie = null)
    {
        $salarie = $this->getUser()->getSalarie();
        $mainenant = $definirDate->aujourdhui();

        $propositions = $this->propositionRepository->findBySalairieDate($salarie, $mainenant);


        $drones = $this->droneRepository->findBy(['entrepris'=>$salarie->getEntreprise(),'actif'=>true]);

        return $this->render('salarie/demcours.html.twig', [
            'propositions' => $propositions,
            'drones' => $drones,
            'date' => $mainenant,



        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/entreprise/details/{id}",name="detailInter")
     * @Security("is_granted('ROLE_SALARIE') and is_granted('ROLE_PREMIUM') or is_granted('ROLE_FREE')")
     */
    public function voirDetailInter(int $id):Response{
        $proposition = $this->propositionRepository->findOneBy(['id'=>$id]);

        return  $this->render("entreprise/detailsInter.html.twig",[
            'proposition'=>$proposition
        ]);
    }

    /**
     * @Route("/validerPrix")
     * @Security("is_granted('ROLE_SALARIE') and is_granted('ROLE_PREMIUM') or is_granted('ROLE_FREE')")
     * @param Mail $mail
     * @param DefinirObjet $definirObjet
     * @param Geoloc $geoloc
     * @param SmsFactor $smsFactor
     * @return JsonResponse
     * @throws TransportExceptionInterface
     */
    public function validerPrix(Mail $mail, DefinirObjet $definirObjet, Geoloc $geoloc,SmsFactor $smsFactor): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), false, 512, JSON_THROW_ON_ERROR);

        $proposition = $this->propositionRepository->findOneBy(['id' => $contenu->idProp]);
        $demandeur = $proposition->getInter()->getIntDem();
        $salarie = $proposition->getSalarie();
        $intervention = $proposition->getInter();
        $maildemandeur = $mail->retourneMailInter($intervention);

        $coordInter = $definirObjet->obtenirCordonnee($intervention);
        $coordSalarie = $definirObjet->obtenirCordonnee($salarie);
        $distance = $geoloc->coutKilometre($coordInter[0], $coordInter[1], $coordSalarie[0], $coordSalarie[1]);
        $proposition->setIndemnite($distance * $salarie->getEntreprise()->getIndeminiteKilometre() / 1000)
                    ->setDatePropose((new \DateTime)->setTimeStamp($contenu->dateInter));
        $reponse = new JsonResponse();
        if ($proposition->getPrix() === null) {
            $message = 'Votre prix est enregistré';
            $mail->mailInter($maildemandeur);
            $text = "DIAG DRONE alerte ! 
                Un Opérateur Télépilote de Drone (OTD) vient de répondre à votre demande d'intervention.
                 Rdv dans votre espace privé pour découvrir sa proposition tarifaire.";
            //$smsFactor->EvoieSms($demandeur->getTelephon()->getNumero(),$text);
        } else {
            $message = 'Votre prix a bien été modifié';
        }

            $proposition->setPrix($contenu->prix);

           $this->manager->persist($proposition);
            $this->manager->flush();

            return $reponse->setData($message);



    }

    /**
     * @Route("/demcour/{idProp}", name="refuser")
     * @Security("is_granted('ROLE_SALARIE') and is_granted('ROLE_PREMIUM') or is_granted('ROLE_FREE')")
     * @param int $idProp
     * @param Mail $mail
     * @param choixTemplate $choixTemplate
     * @return RedirectResponse|Response
     */
    public function refusIntervention( int $idProp, Mail $mail, choixTemplate $choixTemplate)
    {

        $proposition = $this->propositionRepository->findOneById(['id'=>$idProp]);
        $user = $this->getUser();
        $salarie = $user->getSalarie();

        $annulation = new Annulation();
        $annulation->setIntervention($proposition->getInter())
            ->setSalarie($salarie);
        $form = $this->createForm(AnnulationType::class, $annulation);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form['validation']->getData();
            if ($reponse == 'non') {
                return $this->redirectToRoute('entreprise');
            } elseif ($reponse == 'oui') {


                $this->manager->persist($annulation);

                $this->manager->remove($proposition);
                $this->manager->flush();

                return $this->redirectToRoute('demcours');
            }
        }

        return $this->render('salarie/annulation.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/calendrier/{id}", name="calendrier")
     * @isGranted("ROLE_SALARIE")
     * @param int|null $id
     * @return Response
     */
    public function calendrier(int $id = null): Response
    {

        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();


        return $this->render('salarie/calendrier.html.twig', [
            'id' => $id,
            'salarie'=>$salarie,
        ]);
    }

    /**
     * @Route("/mes interventions",name="mesinter")
     * @isGranted("ROLE_SALARIE")
     * @param DefinirAcces $definirAcces
     * @param InterRepo $interRepo
     * @param MandatCerfaRepository $mandatCerfaRepository
     * @param DefinirDate $definirDate
     * @return Response
     * @throws Exception
     */
    public function mesinter( InterRepo $interRepo,
                             MandatCerfaRepository $mandatCerfaRepository,DefinirDate $definirDate)
    {
            $user = $this->getUser();

            $salarie = $user->getSalarie();



        $reseraEnCours = $this->reservationRepository->findInterBySalarie($salarie,'Intervention validée');
        $interdd = $this->reservationRepository->interHdd($salarie,"Intervention validée");
        $tableauFinal = array_merge($reseraEnCours,$interdd);
        $reservationsEnCours = $this->paginator->paginate($tableauFinal, $this->request->query->getInt('page', 1), 2);
        $resaTermine = $interRepo->interSalarie('termine', $salarie);
        $reservationTermine = $this->paginator->paginate($resaTermine, $this->request->query->getInt('page', 1), 2);
        $drone = $salarie->getEntreprise()->getDrones();
        $form = $this->createForm(DepartType::class);
        $etat = $this->etatAbonnementRepository->trouverEtat($salarie->getEntreprise(), $definirDate->aujourdhui());
        $nbreMandat = count($mandatCerfaRepository->nombreMandat($salarie->getEntreprise(), $definirDate->debutDuMois(), $definirDate->finDuMois()));
        $cerfaMax = $etat->getAbonnement()->getCerfaMax();
        $aujourdhui = $definirDate->aujourdhui();
        return $this->render('salarie/mesinter.html.twig', [

            'reservationsEnCours' => $tableauFinal,
            'reservationTermine' => $resaTermine,
            'form' => $form->createView(),
            'date'=>$aujourdhui,
            'drones' => $drone,
            'mandat'=>$nbreMandat,
            'cerfa'=>$cerfaMax,
            'reservations'=>$reseraEnCours
        ]);
    }

    /**
     * @Route("/changerDate")
     * @Security("is_granted('ROLE_SALARIE') and is_granted('ROLE_PREMIUM') or is_granted('ROLE_FREE')")
     * @param Geoloc $geoloc
     * @return JsonResponse
     * @throws Exception
     */
    public function changerDateInter(Geoloc $geoloc): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $intervention = $this->interventionRepository->findOneBy(['id' => $contenu['inter']]);
        $reservation = $this->reservationRepository->findOneBy(['intervention' => $intervention]);
        $latorigine = $reservation->getSalarie()->getAdresse()->getCoordonnees()->getLatitude();
        $lonorigine = $reservation->getSalarie()->getAdresse()->getCoordonnees()->getlongitude();
        $latdestination = $intervention->getAdresse()->getCoordonnees()->getLatitude();
        $londestination = $intervention->getAdresse()->getCoordonnees()->getlongitude();
        $duree = $geoloc->tempsTrajet($latorigine, $lonorigine, $latdestination, $londestination);
        $tempsTrajet = $contenu['dateRdv'] - $duree;
        $dateDepart = new DateTimeImmutable('NOW', new \DateTimeZone('Europe/Paris'));
        $dateDepart->setTimestamp($tempsTrajet);
        $dateInter = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $dateInter->setTimestamp($contenu['dateRdv']);
        $intervention->setRdvAt($dateInter);
        $reservation->setDepart(null)
            ->setDebut($dateDepart);
        $this->manager->persist($intervention);
        $this->manager->persist($reservation);
        $this->manager->flush();

        $reponse = new JsonResponse();
        return $reponse->setData($dateInter);
    }

    /**
     * @Route("/temps/{id}", name="temps")
     * @isGranted("ROLE_SALARIE")
     */
    public function temps($id)
    {

        $reservation = $this->reservationRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(DepartType::class, $reservation);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($reservation);
            $this->manager->flush();
            return $this->redirectToRoute('mesinter');
        }
        return $this->render('salarie/temps.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/absence",name="absence")
     * isGranted("ROLE_SALARIE")
     */

    public function indisponibilite(DefinirDate $definirDate)
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $indispo = new Indisponibilite();
        $indispo->setSalarie($salarie);
        $form = $this->createForm(IndispoType::class, $indispo);

        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($indispo);
            $this->manager->flush();
            return $this->redirectToRoute('calendrier');
        }
        return $this->render('salarie/absence.html.twig', [
            'form' => $form->createView(),
            'salarie' => $salarie,

        ]);
    }


}
