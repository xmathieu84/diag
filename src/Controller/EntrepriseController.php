<?php

namespace App\Controller;



use App\Entity\MissionOdi;
use App\Entity\PackOdi;
use App\Entity\PackOdiPrixTaille;
use App\Entity\PrixOdiMission;
use App\Event\AdressEvent;
use App\Event\MangoPayUserEvent;
use App\Event\UserEvent;
use App\Entity\Adresse;
use App\Entity\Salarie;
use App\Entity\Civilite;
use App\Helper\AppelOffreRepoTrait;
use App\Helper\BanqueRepoTrait;
use App\Helper\DroneRepoTrait;
use App\Helper\ListeInterTypeInterRepoTrait;
use App\Repository\BanqueRepository;
use App\Repository\MissionOdiRepository;
use App\Repository\MissionRepository;
use App\Repository\PrixOdiMissionRepository;
use App\Service\Fichier;
use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Helper\RequestTrait;
use App\Helper\InterRepoTrait;
use App\Service\choixTemplate;
use App\Form\AjoutersalarieType;
use App\Helper\SalarieRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\ReservationRepoTrait;
use App\Service\DefinirDate;
use App\Service\DefinirObjet;
use App\Service\Geoloc;
use App\Service\InterConcurrence;
use App\Service\Mail;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



class EntrepriseController extends AbstractController
{
    use EntityManagerTrait,
        InterRepoTrait, SalarieRepoTrait, EntrepriseRepoTrait, RequestTrait,
        ReservationRepoTrait, etatAboRepoTrait,AppelOffreRepoTrait,BanqueRepoTrait,DroneRepoTrait,ListeInterTypeInterRepoTrait;



    /**
     * @isGranted("ROLE_SALARIE")
     * @Route("/entreprise", name="entreprise")
     *
     * @param choixtemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     * @throws \Exception
     */
    public function index(choixtemplate $choixTemplate, DefinirDate $definirDate,InterConcurrence $concurrence,PrixOdiMissionRepository $prixOdiMissionRepository): Response
    {

        $user = $this->getUser();
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        if ($salarie->getEntreprise()->getCgv()===false){
            return $this->redirectToRoute("alerteContinuerInscription");
        }
        $date = $definirDate->aujourdhui();
        $delai = $definirDate->duree($date, 'P2D');
        $nbreSalarie = $this->salarieRepository->findBy(['entreprise'=>$salarie->getEntreprise()]);
        $etat = $this->etatAbonnementRepository->findByDateEntrepriseNbreOTD($salarie->getEntreprise()->getId(), count($nbreSalarie), $date);
        $encours = $this->interventionRepository->missionEtat($salarie->getEntreprise(),'Intervention validée');
        $termine = $this->interventionRepository->missionEtat($salarie->getEntreprise(),'termine');
        $missionDD = $this->interventionRepository->missionRealisee($salarie->getEntreprise());
        $appelO = $this->appelOffreRepository->nombreAppelByEntreprise($salarie->getEntreprise());
        $reponse = $this->appelOffreRepository->nobreAoChoisi($salarie->getEntreprise());
        $interSansOtd = $this->interventionRepository->interSansProp($delai,$salarie->getEntreprise()->getId());
        $nombrenter = $concurrence->nbreInterSansProp($interSansOtd,$salarie,$salarie->getAdresse()->getCoordonnees());
        $drones = $this->droneRepository->findby(['actif'=>true,"entrepris"=>$salarie->getEntreprise()]);
        $template = $choixTemplate->templateAE($salarie->getEntreprise());
        $banque = $this->banqueRepository->findOneBy(['entreprise'=>$salarie->getEntreprise(),'actif'=>true]);
        $liti = $this->listeInterTypeInterRepository->findBySalarie($salarie);
        if ($salarie->getEntreprise()->getCgv()===true && $banque->getSepaSigne() === null){
            return $this->redirectToRoute('enAttenteDuMandat');
        }
        if ($salarie->getUser()->hasRole('ROLE_ODI')){
            $prixs = $prixOdiMissionRepository->findBySalarie($salarie);
        }
        else{
            $prixs = $salarie->getTauxHoraires();
        }
        return $this->render('entreprise/index.html.twig', [

            'entreprise' => $salarie->getEntreprise(),
            'template'=>$template,
            'etat' => $etat,
            'encours'=>$encours,
            'termine'=>$termine,
            'missionDD'=>$missionDD,
            'appelO'=>$appelO,
            'reponse'=>$reponse,
            'inter'=>$nombrenter,
            'salarie'=>$salarie,
            'drones'=>$drones,
            'prixs'=>$prixs,
            'liti'=>$liti
        ]);
    }

    /**
     * @Route("/entreprise/inscription/{type}" , name="inscritent")
     *
     * @param Fichier $fichier
     * @param DefinirObjet $definirObjet
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function inscriEnt(
        Fichier $fichier,
        DefinirObjet $definirObjet,
        EventDispatcherInterface $eventDispatcher,
        Mail $mail,string $type
    ): Response
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($this->request);
        
        try {

            if ($form->isSubmitted() && $form->isValid() ) {

                $entreprise->setIndeminiteKilometre(0.60);

                if ($form['logo']->getData()) {
                    $nomFichier = $fichier->moveFile($form['logo']->getData(), $this->getParameter('logo_directory'),'logo');
                    $entreprise->setLogo($nomFichier);
                }


                    $entreprise->setCgv(0);

                    $event = new GenericEvent($entreprise);
                    $eventDispatcher->dispatch($event,  AdressEvent::NAME);

                    $AdresseSalarie = $definirObjet->definirAdresse(
                            new Adresse(),
                            $entreprise->getAdresse()->getNumero(),
                            $entreprise->getAdresse()->getNomVoie(),
                            $entreprise->getAdresse()->getCodePostal(),
                            $entreprise->getAdresse()->getVille());

                    $civilite = $definirObjet->definirCivilite(
                            new Civilite(),
                            $form['dirigeant']['type']->getData(),
                            $form['dirigeant']['nom']->getData(),
                            $form['dirigeant']['prenom']->getData()
                        );
                    $salarie = $definirObjet->definirSalarie(new Salarie(), $entreprise, $entreprise->getTelephone(), 'en cours', $AdresseSalarie, $civilite);
                    $salarie->setUser($form['user']->getData());

                    $eventUser = new GenericEvent($salarie->getUser());
                    $eventDispatcher->dispatch($eventUser,  UserEvent::NAME);
                    $salarieEvent = new GenericEvent($salarie);
                    $eventDispatcher->dispatch($salarieEvent,AdressEvent::NAME);
                    $this->manager->persist($civilite);

                    //$mail->mailDocumentEnt($salarie->getUser()->getEmail());
                    $mangoEvent = new GenericEvent($salarie);
                    $eventDispatcher->dispatch($mangoEvent,MangoPayUserEvent::MANGO);
                    if ($entreprise->getFormJuridique() ==='auto-entrepreneur'){
                        $salarie->getUser()->setRoles(["ROLE_ENTREPRISE","ROLE_AE"]);

                    }
                    else{
                        $salarie->getUser()->setRoles(["ROLE_ENTREPRISE"]);
                    }
                    if ($type==="otd"){
                        $salarie->getUser()->addRole('ROLE_OTD');
                    }
                    if ($type==="odi"){
                        $salarie->getUser()->addRole('ROLE_ODI');
                    }
                    
               $this->manager->persist($salarie);
                $this->manager->persist($entreprise);
                $this->manager->flush();
                $mail->mailInscriptionSalarie($salarie);
                return $this->redirectToRoute('activation');
            }
        } catch (\Exception $th) {

            $this->addFlash('alerte', "L'adresse email renseignée est déjà associée à un compte. Si vous avez oublié votre mot de passe veuillez réinitialiser votre mot de passe sur la page de connexion .");
        }



        return $this->render('entreprise/inscription.html.twig', [
            'form' =>  $form->createView(),

        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/entreprise/siret")
     */
    public function siretEnt():JsonResponse{

        $siren = $this->request->getContent();

        $adresse = 'https://entreprise.data.gouv.fr/api/sirene/v3/etablissements/';

        $resulats = json_decode(file_get_contents($adresse.$siren));

        $response = new JsonResponse();

         return $response->setData([
             'adresse'=>[
                 'numero'=>$resulats->etablissement->numero_voie,
                 'nomVoie'=>$resulats->etablissement->type_voie.' '.$resulats->etablissement->libelle_voie.' '.$resulats->etablissement->complement_adresse,
                 'codePostal'=>$resulats->etablissement->code_postal,
                 'ville'=>$resulats->etablissement->libelle_commune
             ],
             'TVA'=>$resulats->etablissement->unite_legale->numero_tva_intra,
             'nom'=>$resulats->etablissement->unite_legale->denomination

         ]);
    }


    /**
     * @Route("/entreprise/asalarie", name="Asalarie")
     * @Security("is_granted('ROLE_ENTREPRISE')")
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function Asalarie(EventDispatcherInterface $eventDispatcher,Geoloc $geoloc,MissionRepository $missionRepository,PrixOdiMissionRepository $prixOdiMissionRepository): Response {
    $otd = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $otd->getEntreprise();
        $salarie = new Salarie();
        $etat = $this->etatAbonnementRepository->findOneBy(['entreprise' => $entreprise, 'abonne' => true]);
        $options = ['user'=>$this->getUser()];
        $form = $this->createForm(AjoutersalarieType::class, $salarie,$options);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salarieUserEvent = new GenericEvent($salarie->getUser());
            $eventDispatcher->dispatch($salarieUserEvent, UserEvent::NAME);

            $salarie->setEntreprise($entreprise);
            $salarie->getUser()->setRoles(['ROLE_SALARIE']);
            $salarie->setValidation('en cours');

            $salarieLocalisationEvent = new GenericEvent($salarie);
            $eventDispatcher->dispatch($salarieLocalisationEvent, AdressEvent::NAME);

            $abonnement = $etat->getAbonnement();
            if ($this->getUser()->hasRole('ROLE_OTD')){

                if ($abonnement->getOtdMax() < count($entreprise->getSalaries())) {
                    $salarieSup = count($entreprise->getSalaries()) - $abonnement->getOtdMax();
                    $montant = $salarieSup * $abonnement->getOtdSup() + $abonnement->getPrix();
                    $etat->setMontant($montant);
                }
                $nom = $etat->getAbonnement()->getNom();
                switch ($nom){
                    case 'So free';
                        $role = 'ROLE_FREE';
                        break;
                    case 'Classic access';
                        $role = 'ROLE_CLASIC';
                        break;
                    case 'Premium network';
                        $role = 'ROLE_PREMIUM';
                        break;
                    case 'Infinite network';
                        $role = 'ROLE_INFINITE';
                        break;
                }
                $salarie->getUser()->addRole($role);
                $salarie->getUser()->addRole('ROLE_OTD');
            }
            else{
                $salarie->getUser()->addRole('ROLE_ODI');
                if ($form->get('copier')->getData()==="Oui"){
                    $salarieCopie = $this->salarieRepository->find($form->get("salarie")->getData());
                    $missions = $missionRepository->findBySalarie($salarieCopie);
                    $prixMissions = $prixOdiMissionRepository->findBySalarie($salarieCopie);
                    $salarie->setPeriInter($salarieCopie->getPeriInter());
                    $distanceInter = $geoloc->distance($salarie->getAdresse()->getCoordonnees()->getLatitude(),$salarie->getAdresse()->getCoordonnees()->getLatitude(),$salarie->getPeriInter());
                    $salarie->getAdresse()->getCoordonnees()->setLatMinInter($distanceInter[0])
                                                            ->setLatMaxInter($distanceInter[1])
                                                            ->setLonMinInter($distanceInter[2])
                                                            ->setLonMaxInter($distanceInter[3]);

                    foreach ($missions as $mission){
                        $missionSalarie = new MissionOdi();
                        $missionSalarie->setOdi($salarie)
                            ->setMission($mission);
                        $this->manager->persist($missionSalarie);
                        if ($form->get('tarif')->getData()){
                            foreach ($prixMissions as $prixMission){

                                if ($prixMission->getMissionOdi()->getMission()===$mission){
                                    $prixMissionSalarie = new PrixOdiMission();
                                    $prixMissionSalarie->setPrix($prixMission->getPrix())
                                        ->setTaille($prixMission->getTaille())
                                        ->setMissionOdi($missionSalarie)
                                        ->setIsValide(false);
                                    $this->manager->persist($prixMissionSalarie);
                                }

                             }
                        }
                    }
                    if ($form->get('pack')->getData()){
                        foreach ($salarieCopie->getPackOdis() as $packOdi){
                            if ($packOdi){
                                $pack = new PackOdi();
                                $pack->setPack($packOdi->getPack())
                                    ->setOdi($salarie);
                                $this->manager->persist($pack);
                            }


                           if ($form->get('tarif')->getData()){
                               foreach ($packOdi->getPackOdiPrixTailles() as $packOdiPrixTaille){
                                   if ($packOdiPrixTaille){
                                       $prixPack = new PackOdiPrixTaille();
                                       $prixPack->setTaille($packOdiPrixTaille->getTaille())
                                           ->setPrix($packOdiPrixTaille->getPrix())
                                           ->setPackOdi($packOdi)
                                           ->setIsValid(false);
                                       $this->manager->persist($prixPack);
                                   }
                               }
                           }
                        }
                    }

                }
            }


            $this->manager->persist($salarie);
            $this->manager->persist($etat);
            $this->manager->flush();
            if ($this->getUser()->hasRole('ROLE_OTD')){
                return $this->redirectToRoute('modifierInter', ['id' => $salarie->getId()]);
            }
            elseif($form->get('copier')->getData() ==='Oui'){
                if (!$form->get('tarif')->getData() && !$form->get('pack')->getData()){
                    return $this->redirectToRoute('rappelInter',['id'=>$salarie->getId()]);
                }else{
                    return $this->redirectToRoute('tarifOdi',['id'=>$salarie->getId()]);
                }
            }
            else{
                return $this->redirectToRoute('modifierInter', ['id' => $salarie->getId()]);
            }
        }
        return $this->render('entreprise/Asalarie.html.twig', [
            'form' => $form->createView(),
            'entreprise' => $entreprise,
            'etat' => $etat
        ]);
    }


    /**
     * @Route("/entreprise/salarie" , name="nsalarie")
     *
     * @Security("is_granted('ROLE_ENTREPRISE')")
     * @return Response
     */
    public function ListeSalarie():Response
    {
        $otd = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $otd->getEntreprise();

        return $this->render('entreprise/salarie.html.twig', [
            'entreprise' => $entreprise,
            'otd'=>$otd
        ]);
    }

    /**
     * @Route("/entreprise/suppressionS/{id}" , name="ssalarie")
     * @isGranted("ROLE_ENTREPRISE")
     * suppression d'un salarie
     *
     * @param null $id
     * @return RedirectResponse
     */
    public function Ssalarie($id = null):RedirectResponse
    {
        $Salarie = $this->salarieRepository->findOneBy(['id' => $id]);
        $this->manager->remove($Salarie);
        $this->manager->flush();

        return $this->redirectToRoute('nsalarie');
    }

    /**
     * @return Response
     * @Route("/entreprise/changementValidé",name="changeValid")
     * @isGranted ("ROLE_SALARIE")
     */
    public function validerChangement():Response{
        return  $this->render("entreprise/validerInfo.html.twig");
    }
}
