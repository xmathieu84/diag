<?php

namespace App\Controller;


use App\Entity\AboTotalInsti;
use App\Entity\Agent;
use App\Entity\Demandeur;
use App\Entity\FactureInsti;
use App\Event\AdressEvent;
use App\Event\DemandeurEvent;

use App\Event\UserEvent;
use App\Form\DemandeurType;
use App\Helper\AboRepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\AmbassadeurRepoTrait;
use App\Helper\CodePromoRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\RapportRepoTrait;
use App\Repository\DemandeurRepository;
use App\Repository\InterDiagRepository;
use App\Repository\MailPrefectureRepository;
use App\Repository\PourcentageRepository;
use App\Repository\ProBtpRepository;
use App\Service\AbonnementInstitutionnel;
use App\Service\DefinirDate;
use App\Service\FacturePdfService;
use App\Service\Fichier;
use App\Service\PublicitService;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\PaginatorTrait;
use App\Helper\RequestTrait;
use App\Helper\ReservationRepoTrait;
use App\Repository\RapportRepository;
use App\Service\choixTemplate;
use App\Service\codeActivation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\Mail;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DemandeurController
 * @package App\Controller
 */
class DemandeurController extends AbstractController
{
    use RequestTrait, EntityManagerTrait,AmbassadeurRepoTrait,DemandeurRepoTrait, ReservationRepoTrait, InterRepoTrait, PaginatorTrait,AboRepoTrait,AgentRepoTrait,RapportRepoTrait,CodePromoRepoTrait;


    /**
     * @Route("/inscription/{type}", name="inscriptionDem")
     *
     * @param string $type
     * @param EventDispatcherInterface $eventDispatcher
     * @param DefinirDate $definirDate
     * @return Response
     * @throws MpdfException
     */
    public function inscription(string $type, EventDispatcherInterface $eventDispatcher,Mail $mail,
                                DefinirDate $definirDate,AbonnementInstitutionnel $abonnementInstitutionnel,
                                codeActivation $codeActivation,MailPrefectureRepository $repository,
                                InterDiagRepository $interDiagRepository,PourcentageRepository $pourcentageRepository,FacturePdfService $facturePdfService): Response
    {
        $demandeur = new Demandeur();
        $form = $this->createForm(DemandeurType::class, $demandeur);
        $form->handleRequest($this->request);
        $inter = $this->request->query->get('id');

         try {

        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'consultant' || $type === 'demandeur') {
                if ($form->get('profil')->getData()==="Particulier propriétaire"){
                    $demandeur->setNom($type);

                }
                else{
                    $demandeur->setNom($form->get('nom')->getData());
                }

                    $demandeur->setProfil($form['profil']->getData());
                    $demandeur->setUser($form['user']->getData());
                    $accesDemandeurEvent = new GenericEvent($demandeur->getUser());
                    $eventDispatcher->dispatch($accesDemandeurEvent,  UserEvent::NAME);
                    $mangoEvent = new GenericEvent($demandeur);
                    $eventDispatcher->dispatch($mangoEvent,  DemandeurEvent::NAME);

                }
                elseif ($type === 'institutionnel' || $type === 'Grand compte') {
                    $date = $definirDate->aujourdhui();
                    $demandeur->setNom($form['nom']->getData());

                    if ($type ==='Grand compte'){
                        $demandeur->setProfil($form['profilGc']->getData());
                    }else{
                        $demandeur->setProfil($form['profilInsti']->getData());
                    }

                    $agent = new Agent();
                    $agent->setDemandeur($demandeur)
                        ->setUser($form['user']->getData())
                        ->setCivilite($demandeur->getCivilite())
                        ->setCgv(false);
                    $demandeur->setCivilite(null);

                    $ambassadeur=null;
                    $code = null;
                    if ($type ==='institutionnel'){
                        if ($form['codePromo']->getData()){
                            $code = $this->codePromoRepository->findOneBy(['codeReduc'=>$form['codePromo']->getData()]);
                            $ambassadeur = $this->ambassadeurRepository->ambassadeurByHabitant($form['habitant']->getData(),$form['codePromo']->getData());
                        }
                        if ($ambassadeur){
                            $cp = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$form['cpAmbassadeur']->getData()));
                            $departement = $repository->findOneBy(['numeroDepartement'=>$cp[0]->codeDepartement]);
                            $nbreInsti = $this->demandeurRepository->findAmbassadeurInsti($departement,$ambassadeur);
                        }

                        if ($code){
                            $abonnement = $abonnementInstitutionnel->abonnementInstiPromo($code,$demandeur);
                        }
                        elseif ($ambassadeur){
                            if (count($nbreInsti)<$ambassadeur->getMaximum()){
                                $abonnement= $abonnementInstitutionnel->ambassadeurGrandCompte($ambassadeur,$demandeur);
                                $demandeur->setAmbassadeurInsti($ambassadeur);
                            }
                            else{
                                $abonnement = $abonnementInstitutionnel->choixAbonnement($demandeur,$form['habitant']->getData());
                            }

                        }
                        elseif ($demandeur->getProfil() ==='Région'){
                            $abonnement = $abonnementInstitutionnel->choixAbonnement($demandeur,5000008);
                        }
                        else{
                            $abonnement = $abonnementInstitutionnel->choixAbonnement($demandeur,$form['habitant']->getData());
                        }
                        $agent->getUser()->setRoles(['ROLE_INSTITUTION', "ROLE_MANITOU"]);


                    }
                    if ($type ==='Grand compte'){
                        if ($form['codePromo']->getData()){
                            $code = $this->codePromoRepository->findOneBy(['profil'=>$form['profilGc']->getData(),'codeReduc'=>$form['codePromo']->getData()]);
                            $ambassadeur = $this->ambassadeurRepository->findOneBy(['profil'=>$form['profilGc']->getData(),'codeReduc'=>$form['codePromo']->getData()]);
                        }
                        if ($ambassadeur){
                            $cp = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$form['cpAmbassadeur']->getData()));
                            $departement = $repository->findOneBy(['numeroDepartement'=>$cp[0]->codeDepartement]);
                            $grandCompte = $this->demandeurRepository->findAmbassadeurGc($departement,$ambassadeur);
                        }
                        $agent->getUser()->setRoles(['ROLE_GRANDCOMPTE', "ROLE_MANITOU"]);


                        if ($code){
                          $abonnement = $abonnementInstitutionnel->abonnementInstiPromo($code,$demandeur);
                        }
                        elseif ($ambassadeur && count($grandCompte)<$ambassadeur->getMaximum()){
                            $abonnement = $abonnementInstitutionnel->ambassadeurGrandCompte($ambassadeur,$demandeur);
                            $demandeur->setAmbassadeurGrandCompte($ambassadeur);

                        }
                        else{
                            $abonnement = $abonnementInstitutionnel->choixAbonnementGc($demandeur,$form['utilisateur']->getData(),$demandeur->getProfil());
                        }



                    }
                    if ($inter){
                        $diag = $interDiagRepository->find($inter);
                        $diag->setDemandeur($demandeur);
                        $taux = $pourcentageRepository->findOneBy(['nom'=>"acompte"]);
                        $factureInter = $facturePdfService->createFactureDiag($diag,'inter');
                        if ($demandeur->getUser()->hasRole('ROLE_DEMANDEUR')||$demandeur->getUser()->hasRole('ROLE_CONSULTANT')){
                            $factureAcompte = $facturePdfService->createFactureDiag($diag,'acompte');
                            $diag->setFactureAcompte($factureAcompte)
                                ->setAcompte($diag->getPrix()*($taux->getTaux()/100))
                                ->setStatut("en attente de paiement");
                        }
                        else{
                            $diag->setStatut('Intervention validée');
                        }
                        $diag->setFacture($factureInter);
                    }
                    $accesDemandeurEvent = new GenericEvent($form['user']->getData());
                    $eventDispatcher->dispatch($accesDemandeurEvent, UserEvent::NAME);

                   $this->manager->persist($agent);

                   $this->manager->persist($abonnement);

                }
            $localisationEvent = new GenericEvent($demandeur);
            $eventDispatcher->dispatch($localisationEvent,  AdressEvent::NAME);
            switch ($type){
                case 'consultant':
                    $demandeur->getUser()->setRoles(['ROLE_CONSULTANT']);
                    break;
                case 'demandeur':
                    $demandeur->getUser()->setRoles(['ROLE_DEMANDEUR']);
                    break;
                case 'institutionnel':
                    $agent->getUser()->setRoles(['ROLE_INSTITUTION',"ROLE_MANITOU"]);
                    break;
                case 'Grand compte' :
                    $agent->getUser()->setRoles(['ROLE_GRANDCOMPTE',"ROLE_MANITOU"]);
                    break;
            }
            if ($demandeur->getProfil()==='Syndicat de co-propriété'){
                $agent->setIdentifiant($codeActivation->identifiantDemandeur());
                $agent->getUser()->addRole('ROLE_SYNDICAT');

            }


         $this->manager->persist($demandeur);

           $this->manager->flush();
            if ($type == 'consultant' || $type == 'demandeur'){
                $mail->confirmerMail($demandeur->getUser()->getCodeActivation(), $demandeur->getUser()->getEmail());
            }elseif ($type === 'institutionnel' || $type === 'Grand compte'){
                $mail->mailInscriptionAgent($agent);
            }
           return $this->redirectToRoute('activation');
        }
         } catch (\Exception $th) {
             dump($th);
             if ($th->getCode() ===1062){
                 $this->addFlash('alerte', "L'adresse email renseignée est déjà utilisée par un autre compte");
             }else{
                 $this->addFlash('alerte', 'Un ou plusieurs champs ne sont pas correctement remplis');
             }

        }



        return $this->render('demandeur/inscription.html.twig', [

            'form' => $form->createView(),
            'type' => $type
        ]);
    }



    /**
     * @Route("/demandeur", name="demandeur")
     * @Security ("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT')")
     *
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function index(choixTemplate $choixTemplate,DemandeurRepository $demandeurRepository,
                          Fichier $fichier,PublicitService $pub,RequestStack $requestStack,InterDiagRepository $interDiagRepository): Response
    {

        $demandeur = $demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $template = $choixTemplate->templateDem($this->getUser());
        $interDiags = $interDiagRepository->findBy(['demandeur'=>$demandeur,'statut'=>"en attente de paiement"]);

        //$intervention = $this->interventionRepository->findInterForPub($demandeur);
        if (!$demandeur->getCgv()) {
            return $this->redirectToRoute('terminerInscription');
        } else {
            return $this->render('demandeur/index.html.twig', [
                'user' => $this->getUser(),
                'demandeur' => $demandeur,
                'template' => $template,
                "interDiags"=>$interDiags

            ]);
        }
    }

    /**
     * @Route("/demandeur/mesdiags/{code}", name="demandeur_diag")
     * @Security("is_granted('ROLE_NIVEAU1') and is_granted('ROLE_ABONNE')  or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_NIVEAU1GC') and is_granted('ROLE_ABONNE')")
     *
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function mesDiags(choixTemplate $choixTemplate,InterDiagRepository $interDiagRepository,string $code=null): Response
    {
        $template = $choixTemplate->templateDem($this->getUser());
        if (!$code){
            $demandeur = $template[1];
        }else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
            $demandeur = $agent->getDemandeur();
        }
        $interventions = $this->interventionRepository->findBy(['intDem' => $demandeur, 'statuInter' => 'Intervention validée']);
        $reservationValidee = $this->reservationRepository->findBy(['intervention' => $interventions]);
        $reservationVirement = $this->reservationRepository->findByIntervention('en attente de virement', $demandeur);
        $interventionTerminee = $this->interventionRepository->findBy(['intDem' => $demandeur, 'statuInter' => 'termine']);
        $resaTermine = $this->reservationRepository->findBy(['intervention' => $interventionTerminee]);
        $interDiagEnCours=  $interDiagRepository->findBy(["statut"=>"Intervention validée","demandeur"=>$demandeur]);
        $interDiagTermines = $interDiagRepository->findBy(["statut"=>"Intervention terminée","demandeur"=>$demandeur]);

        return $this->render('demandeur/mesdiags.html.twig', [
            'user' => $this->getUser(),
            'reservations' => $reservationValidee,
            'reservationTermine' => $resaTermine,
            'resaVirement' => $reservationVirement,
            'template' => $template[0],
            'code'=>$code,
            "interDiagEnCours"=>$interDiagEnCours,
            "interDiagTermines"=>$interDiagTermines,
        ]);
    }

    /**
     * @Route("/interventionEnAttenteDePaiement",name="enAttenteDePaiement")
     * @isGranted("ROLE_DEMANDEUR")
     * @param DemandeurRepository $demandeurRepository
     * @param InterDiagRepository $interDiagRepository
     * @return Response
     */
    public function enAttenteDePaiement(DemandeurRepository $demandeurRepository,InterDiagRepository $interDiagRepository): Response
    {

        $demandeur = $demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $reservationAPayer = $this->reservationRepository->findByInterStatut($demandeur,'En attente de paiement');
        $reservationVirement = $this->reservationRepository->findByInterStatut($demandeur,'en attente de virement');
        $diagsCbs = $interDiagRepository->findBy(['statut'=>'en attente de paiement','demandeur'=>$demandeur]);
        $diagVirement =  $interDiagRepository->findBy(['statut'=>'en attente de virement','demandeur'=>$demandeur]);

        return $this->render('demandeur/interApayer.html.twig', [
            'resaApayers' => $reservationAPayer,
            'user' => $this->getUser(),
            'resaVirements' => $reservationVirement,
            'diagCns'=>$diagsCbs,
            'diagVirements'=>$diagVirement
        ]);
    }

    /**
     * @Route("/demandeur/encours/{code}", name="demandeur_encours")
     * @Security("is_granted('ROLE_NIVEAU1') and is_granted('ROLE_ABONNE') or is_granted('ROLE_DEMANDEUR')")
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function enCours(choixTemplate $choixTemplate,string $code = null): Response
    {
        $template = $choixTemplate->templateDem($this->getUser(),$code);
        $demandeur = $template[1];


        $reservationEncours =  $this->reservationRepository->findByInterStatut($demandeur,'Nouvelle demande');

        $resaEncours = $this->paginator->paginate($reservationEncours, $this->request->query->getInt('page', 1), 2);


        return $this->render('demandeur/encours.html.twig', [
            'user' => $this->getUser(),
            'reservations' => $resaEncours,
            'template' => $template[0],
            'code'=>$code

        ]);
    }

    /**
     * @Route("/nombreInter")
     * @Security("is_granted('ROLE_NIVEAU1') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_NIVEAU1GC') or is_granted('ROLE_BTP')")
     * @param choixTemplate $choixTemplate
     * @return JsonResponse
     */
    public function nombreInter(choixTemplate $choixTemplate,InterDiagRepository $interDiagRepository): JsonResponse
    {
        $template = $choixTemplate->templateDem($this->getUser());
        $demandeur  = $template[1];
        $interApayer = count($this->interventionRepository->findBy(['intDem' => $demandeur, 'statuInter' => 'En attente de paiement']));
        $interVirement = count($this->interventionRepository->findBy(['intDem' => $demandeur, 'statuInter' => 'en attente de virement']));
        $interEnCours = count($this->interventionRepository->interDemandeur($demandeur));
        $interValide = count($this->interventionRepository->findBy(['intDem' => $demandeur, 'statuInter' => 'Intervention validée']));
        $interTermine = count($this->interventionRepository->findBy(['intDem' => $demandeur, 'statuInter' => 'termine']));
        $diagTermine = count($interDiagRepository->findBy(['statut'=>'Intervention terminée','demandeur'=>$demandeur]));
        $diagvalide = count($interDiagRepository->findBy(['statut'=>'Intervention validée','demandeur'=>$demandeur]));
        $diagPaiement = count($interDiagRepository->findBy(['statut'=>'Intervention en attente de paiement','demandeur'=>$demandeur]));
        return (new JsonResponse())->setData([
            'payer' => $interApayer + $interVirement +$diagPaiement,
            'enCours' => $interEnCours ,
            'termine' => $interTermine + $interValide + $diagTermine+$diagvalide
        ]);
    }

    /**
     * @Route("/liste Rapport/{code}",name="listeRapportDem")
     * @Security("is_granted('ROLE_NIVEAU1') or is_granted('ROLE_DEMANDEUR')")
     * @param RapportRepository $rapportRepository
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function listeRapport(RapportRepository $rapportRepository, choixTemplate $choixTemplate,$code = null): Response
    {
        $template = $choixTemplate->templateDem($this->getUser());
        $demandeur = $template[1];
        $rapports = $rapportRepository->findByDemandeur($demandeur);

        return $this->render('demandeur/listeRapport.html.twig', [
            'rapports' => $rapports,
            'user' => $this->getUser(),
            'template' => $template[0],
            'code'=>$code
        ]);
    }

    /**
     * @Route("/envoyerRapport")
     * @Security("is_granted('ROLE_NIVEAU1') or is_granted('ROLE_DEMANDEUR')")
     * @param Mail $mail
     * @param RapportRepository $rapportRepository
     * @param codeActivation $codeActivation
     * @return JsonResponse
     */
    public function envoyerRapport(Mail $mail, RapportRepository $rapportRepository, codeActivation $codeActivation): JsonResponse
    {

        $content = json_decode($this->request->getContent(), true);
        $rapport = $rapportRepository->findOneBy(['id' => $content['idRapport']]);
        if ($rapport->getCodeRecherche()) {
            $codeRecherche = $rapport->getCodeRecherche();
        } else {
            $codeRecherche = $codeActivation->codeRapport();
        }

        $codeUnique = $codeActivation->generer();
        $mail->mailConsultant($content['Email'], $codeRecherche, $codeUnique);
        $rapport->setCode2unique($codeUnique)
            ->setCodeRecherche($codeRecherche);
        $this->manager->persist($rapport);
        $this->manager->flush();
        $response = new JsonResponse();
        return $response->setData("Un lien a été envoyé aux adresses mail indiquées.");
    }

    /**
     * @param choixTemplate $choixTemplate
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/streamRapport/{id}",name="streamRapportDemandeur")
     */
    public function streamRapportDemandeur(choixTemplate $choixTemplate,$id):Response{

        $template = $choixTemplate->templateDem($this->getUser());
        $rapport = $this->rapportRepository->findForDemandeur($id,$template[1]);
        return $this->render('consultant/voirRapport.html.twig',[
            'rapport'=>$rapport,
            'template'=>$template[0]
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/pubCible")
     */
    public function PubCiblee(ProBtpRepository $repository):JsonResponse{
        $demandeur = $this->getUser()->getDemandeur();
        $coordoneesDem = $demandeur->getAdresse()->getCoordonnees();
        $interventions = $this->interventionRepository->findBy(['intDem'=>$demandeur,'statuInter'=>'Intervention validée']);
        $lastInter = end($interventions);
        $travaux = $lastInter->getTravauxes();

        $listeBtp = [];
        $test = [2,5,4,8,2,4];
        foreach ($travaux as $trvx){

            $proBtps = $repository->findForPubcibleInter($trvx,$coordoneesDem->getLatitude(),$coordoneesDem->getLongitude());
            foreach ($proBtps as $proBtp){

                if ($proBtp->getDemandeur()->getLogo()){
                    $image = $proBtp->getDemandeur()->getLogo();
                }
                else{
                    $image = $proBtp->getBandeauPub();
                }
                $agent = $this->agentRepository->findProBtp($proBtp->getDemandeur());

               array_push($listeBtp,['image'=>$image,'site'=>$proBtp->getSiteWeb(),'email'=>$agent->getUser()->getEmail()]);
            }
        }

         return new JsonResponse(array_unique($listeBtp,SORT_REGULAR));
    }

}
