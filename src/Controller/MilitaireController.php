<?php


namespace App\Controller;


use App\Form\ChangePassArmeeType;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;

use App\Service\DefinirDate;
use App\Service\Geoloc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class MilitaireController extends AbstractController
{
    use InterRepoTrait, RequestTrait, EntityManagerTrait;

    /**
     * @return mixed
     * @Route("/militaire/accueil",name="accueilMilitaire")
     */
    public function accueilMiltaire(): Response
    {
        $user = $this->getUser();

        return $this->render('militaire/accueil.html.twig', ['user' => $user]);
    }

    /**
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @Route("militaire/interDujour")
     */
    public function interJour(DefinirDate $definirDate): JsonResponse
    {
        $dateDebutJour = $definirDate->debutJournee();
        $dateFinJour = $dateFinJournee = $definirDate->finDeJournee();
        $interJour = $this->interventionRepository->interDuJour($dateDebutJour, $dateFinJour);

        $dateFinSemaine = $definirDate->duree($dateFinJournee, 'P7D');
        $interSemaine = $this->interventionRepository->interDuJour($dateDebutJour, $dateFinSemaine);

        $debutMois = $definirDate->debutDuMois();
        $finMois = $definirDate->finDuMois();
        $interMois = $this->interventionRepository->InterDuJour($debutMois, $finMois);

        $nombreInterJour = sizeof($interJour);
        $nbreInterSemaine = sizeof($interSemaine);
        $nbreInterMois = sizeof($interMois);
        $reponse = new JsonResponse();
        return $reponse->setData([
            'nombreInterJour' => $nombreInterJour,
            'nbreInterSemaine' => $nbreInterSemaine,
            'nbreInterMois' => $nbreInterMois
        ]);
    }

    /**
     * @return Response
     * @Route("/militaire/changer_mot_de_passe",name="changePass")
     */
    public function changePass(UserPasswordHasherInterface $encoder): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(ChangePassArmeeType::class);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pass = $encoder->hashPassword($user, $form['password']->getData());
            $user->setPassword($pass);
            $this->manager->persist($user);
            $this->manager->flush();
            return $this->redirectToRoute('accueilMilitaire');
        }
        return $this->render('militaire/chnangePass.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @return Response
     * @Route("/militaire/carteInter",name="carte_inter")
     *
     */
    public function carteInter()
    {
        $user = $this->getUser();
        return $this->render('militaire/carte.html.twig', ['user' => $user]);
    }

    /**
     * @param Geoloc $geoloc
     * @param DefinirDate $definirDate
     *
     * @throws \Exception
     * @Route("/militaire/afficherCarte")
     */
    public function afficherCarte(Geoloc $geoloc, DefinirDate $definirDate)
    {
        $user = $this->getUser();
        $contenu = json_decode($this->request->getContent(), true);
        $dateDebutJournee = $definirDate->debutJournee();
        $dateFinJournee = $definirDate->finDeJournee();
        $inter = [];
        switch ($contenu['dateInter']) {
            case '1jour':
                $dateDebut = $dateDebutJournee;
                $dateFin = $dateFinJournee;
                break;
            case '7jours':
                $dateDebut = $dateDebutJournee;
                $dateFin = $definirDate->duree($dateFinJournee, 'P7D');
                break;

            case '1mois':
                $dateDebut = $definirDate->debutDuMois();
                $dateFin = $definirDate->finDuMois();
        }

        $latBase = $user->getMiltaire()->getAdresse()->getCoordonnees()->getLatitude();
        $lonBase = $user->getMiltaire()->getAdresse()->getCoordonnees()->getLongitude();
        $latArray = [$latBase, $lonBase];
        $positionMax = $geoloc->distance($latBase, $lonBase, 1200);

        $interventions = $this->interventionRepository->listeInterMilitaire(
            $positionMax[0],
            $positionMax[1],
            $positionMax[2],
            $positionMax[3],
            $dateDebut,
            $dateFin
        );

        foreach ($interventions as $intervention) {
            $intervention->jsonSerialize();
            array_push($inter, [
                $intervention->getAdresse()->getCoordonnees()->getLatitude(),
                $intervention->getAdresse()->getCoordonnees()->getLongitude(),
                $intervention->getDrone()->getNomFabriquant(),
                $intervention->getDrone()->getTypeDrone(),
                $intervention->getDrone()->getNumeroDgac(),
                $intervention->getReservation()->getSalarie()->getCivilite()->getNom(),
                $intervention->getReservation()->getSalarie()->getCivilite()->getPrenom(),
                $intervention->getReservation()->getSalarie()->getTelephone()->getNumero(),
                $intervention->getAdresse()->getNumero(),
                $intervention->getAdresse()->getNomVoie(),
                $intervention->getAdresse()->getCodePostal(),
                $intervention->getAdresse()->getVille(),
                $intervention->getRdvAT()->format('d/m/Y H:i'),
                $intervention->getReservation()->getDepart()->format('d/m/y H:i')

            ]);
        }

        $reponse = new JsonResponse();
        return $reponse->setData([
            'center' => $latArray,
            'inter' => $inter
        ]);
    }
}
