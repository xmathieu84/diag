<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Civilite;
use App\Entity\Coordonnees;
use App\Entity\Demandeur;
use App\Entity\Intervention;
use App\Entity\Reservation;
use App\Helper\DroneRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\ListeInterRepoTrait;
use App\Helper\ListeInterTypeInterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Helper\TypeInterRepoTrait;
use App\Repository\MailPrefectureRepository;
use App\Service\choixTemplate;
use App\Service\Geoloc;
use DateTime;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class InerventionPersoController extends AbstractController
{
    use ListeInterRepoTrait, EntityManagerTrait, RequestTrait, ListeInterTypeInterRepoTrait, TypeInterRepoTrait, DroneRepoTrait, SalarieRepoTrait,EntrepriseRepoTrait;


    /**
     * @Route("/salarie/interPerso",name="interPerso")
     * @isGranted("ROLE_SALARIE")
     *
     * Formulaire d'ajout d'une intervention personnalisée avec un Demandeur non inscrit sur l'application.
     * 
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function interPerso(choixTemplate $choixTemplate): Response
    {
        $user = $this->getUser();
        $template = $choixTemplate->templateSal($user);
        $listeInter = $this->listeInterRepository->findAll();
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $entreprise = $salarie->getEntreprise();

        return $this->render('salarie/interventionPerso.html.twig', [
            'listeInters' => $listeInter,
            'template' => $template[1],
            'drones' => $entreprise->getDrones(),
            'salarie' => $salarie
        ]);
    }

    /**
     * @route("/preciserInter")
     * @isGranted("ROLE_SALARIE")
     * 
     *  Récupération du type d'intervention pour la précision des liste d'interventions.
     * Données recupérées et renvoyées en AJAX.
     * Fichier JS : interventionPerso.js
     * 
     * @return JsonResponse
     */
    public function preciserInter(): JsonResponse
    {
        $typeInter = [];
        $id = $this->request->getContent();
        $listeInter = $this->listeInterRepository->findOneBy(['id' => $id]);
        $liti = $this->listeInterTypeInterRepository->findBy(['listeInter' => $listeInter]);
        foreach ($liti as $ListeInterTypeInter) {

            array_push($typeInter, $ListeInterTypeInter->getTypeInter()->jsonSerialize());
        }
        $reponse = new JsonResponse();

        return $reponse->setData($typeInter);
    }


    /**
     * @Route("/enregistrerInter")
     * @isGranted("ROLE_SALARIE")
     *
     * Ajout d'une intervention personalisées d'un OTD.
     * Le demandeur est enregistré mais il n'est pas inscrit en tant qu'utilisateur de l'application.
     * Requète en AJAX.
     * Fichier JS : interventionPerso.js
     *
     * @param Geoloc $geoloc
     * @return JsonResponse
     * @throws \Exception
     */
    public function enregistrerInter(Geoloc $geoloc,MailPrefectureRepository $repository): JsonResponse
    {
        $inter = new Intervention();
        $adresse = new Adresse();
        $reservation = new Reservation();
        $demandeur = new Demandeur();
        $civilite = new Civilite();
        $coordonnees = new Coordonnees();
        $intemperie = [];
        $date  = $this->request->get('date');
        $salarie = $this->salarieRepository->findOneBy(['id' => $this->request->get('idSalarie')]);
        $dateInter = new DateTimeImmutable('NOW',new \DateTimeZone('Europe/Paris'));
        $dateInter->setTimestamp($date);

        if ($this->request->get('dateIntemp')){
            $dateIntemp = new DateTime();
            $dateIntemp->setTimestamp($this->request->get('dateIntemp'));
            $intemperie[]= ['dateIntemp'=>$dateIntemp];
            $intemperie[]=['intemp'=>$this->request->get('intemperie')];

            $inter->setIntemperie($intemperie);
        }
        $cp = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$this->request->get('codePostal')));
        $departement = $repository->findOneBy(['numeroDepartement'=>$cp[0]->codeDepartement]);
        $idListeInter = $this->request->get('idListeInter');
        $idTypeInter = $this->request->get('idTypeInter');
        $precision = $this->request->get('precision');
        $idDrone = $this->request->get('idDrone');
        $numero = $this->request->get('numero');
        $nomVoie = $this->request->get('nomVoie');
        $cp = $this->request->get('codePostal');
        $ville = $this->request->get('ville');

        $adresse->setNumero($numero)
            ->setNomVoie($nomVoie)
            ->setVille($ville)
            ->setCodePostal($cp)
            ->setDepartement($departement);

        $inter->setAdresse($adresse)
            ->setType('hdd');

        $demandeur->setCivilite($civilite)
                    ->setAdresse($adresse);
        $civilite->setType($this->request->get('genre'))
                ->setNom($this->request->get('nom'))
                ->setPrenom($this->request->get('prenom'));

        $position = $geoloc->geolocalisation($inter);
        $coordonnees->setLatitude($position[0])
            ->setLongitude($position[1]);
        $adresse->setCoordonnees($coordonnees);
        if ($idDrone) {
            $drone  = $this->droneRepository->findOneBy(['id' => $idDrone]);
            $inter->setDrone($drone);
        }
        $listeInter = $this->listeInterRepository->findOneBy(['id' => $idListeInter]);
        $typeinter = $this->typInterRepository->findOneBy(['id' => $idTypeInter]);
        $inter->setTypeInter($typeinter)
            ->setListeInter($listeInter)
            ->setInterPrecision($precision)
            ->setRdvAT($dateInter)
            ->setStatuInter('Intervention validée')
            ->setIntDem($demandeur);

        $reservation->setDebut($dateInter)
            ->setIntervention($inter)
            ->setSalarie($salarie);
        $this->manager->persist($civilite);
        $this->manager->persist($demandeur);
        $this->manager->persist($coordonnees);
        $this->manager->flush();
        $this->manager->persist($inter);
        $this->manager->persist($reservation);
       $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(5);
    }
}
