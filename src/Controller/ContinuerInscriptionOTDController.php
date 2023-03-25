<?php

namespace App\Controller;

use App\Entity\AlphaTango;
use App\Entity\Banque;
use App\Entity\Entreprise;
use App\Entity\FactureInsti;
use App\Entity\LicenceDgac;
use App\Form\BanqueType;
use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Repository\BanqueRepository;
use App\Repository\DemandeurRepository;
use App\Repository\DetailPrixRepository;
use App\Repository\FactureInstiRepository;
use App\Repository\FamilleDiagRepository;
use App\Repository\MissionOdiRepository;
use App\Repository\MissionRepository;
use App\Repository\PackSupRepository;
use App\Repository\PourcentageRepository;
use App\Repository\TypeDiagRepository;
use App\Service\FacturePdfService;
use App\Service\Geoloc;
use App\Service\Fichier;
use App\Service\Mail;
use App\Service\MangoPayService;
use Doctrine\ORM\NonUniqueResultException;
use JsonException;
use Mpdf\Mpdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Helper\RequestTrait;
use App\Service\choixTemplate;
use App\Helper\EntityManagerTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\ListeInterTypeInterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Helper\TauxHoraireRepoTrait;
use App\Repository\ListeInterRepository;
use App\Service\ChoixTypeInterSalarie;
use App\Service\DefinirDate;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ContinuerInscriptionOTDController
 * @package App\Controller
 */
class ContinuerInscriptionOTDController extends AbstractController
{

    use EntityManagerTrait, RequestTrait,
        SalarieRepoTrait,ListeInterTypeInterRepoTrait, TauxHoraireRepoTrait, etatAboRepoTrait,
        AgentRepoTrait,AboTotalInstiRepoTrait;


    /**
     * @Route("/continuer/inscription/intervention/", name="phase2")
     * @Route("/modifier/intervention/{id}",name="modifierInter")
     * @isGranted("ROLE_SALARIE")
     *
     * @param null $id
     */
    public function continuerInscription(choixTemplate $choixTemplate, ListeInterRepository $listeInterRepository,FamilleDiagRepository $familleDiagRepository,$id=null):Response
    {
        $listesInter = $listeInterRepository->findAll();
        if ($id){
            $salarie = $this->salarieRepository->find($id);
        }
        else{
            $salarie = $this->getUser()->getSalarie();
        }
        $familles= $familleDiagRepository->findAll();

        $templateSalarie = $choixTemplate->templateAE($salarie->getEntreprise());
        $template = $templateSalarie;
        $taux = $salarie->getTauxHoraires();


        return $this->render('continuer_inscription_otd/index.html.twig', [
            'listeInters' => $listesInter,
            'entreprise' => $salarie->getEntreprise(),
            'template' => $template,
            'tauxs' => $taux,
            'salarie' => $salarie,
            'familles'=>$familles

        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/distance")
     * @isGranted("ROLE_SALARIE")
     */
    public function distanceEtLicence(): JsonResponse
    {
        $id = $this->request->getContent();
        $aerien = false;
        $salarie = $this->salarieRepository->findOneBy(['id' => $id]);
        $odi =false;
        if ($salarie->getPeriInter() === null) {
            $perimetre = false;
        } else {
            $perimetre = true;
        }
        foreach ($salarie->getTauxHoraires() as $taux) {
            if ($taux->getInter()->getListeInter()->getNom() === 'interventions aériennes') {
                $aerien = true;
            }
        }
        if ($salarie->getDetailPrixes() !== null){
            $odi =true;
        }

        return (new JsonResponse())->setData(['perimetre' => $perimetre, 'aerien' => $aerien,'odi'=>$odi]);
    }

    /**
     * @return JsonResponse
     * @Route("/recupererInter")
     * @isGranted("ROLE_SALARIE")
     */
    public function recupererInter(MissionOdiRepository $missionOdiRepository): JsonResponse
    {
        $content = json_decode($this->request->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $OTD = $this->salarieRepository->find($content->id);
        $listeTaux = [];
        if ($content->type ==='OTD'){
            $tauxHoraires = $this->tauxHoraireRepository->findBySalarie($OTD);
            foreach ($tauxHoraires as $tauxHoraire) {
                $listeTaux[] = $tauxHoraire->getInter()->getId();
            }
        }
        if ($content->type ==="odi"){
            $missions = $missionOdiRepository->findBy(['odi'=>$OTD]);
            foreach ($missions as $mission){
                $listeTaux[]=$mission->getMission()->getId();
            }
        }

        return (new JsonResponse())->setData($listeTaux);
    }

    /**
     * @Route("/ajoutTypeInterSalarie")
     * @isGranted("ROLE_SALARIE")
     *
     * @param ChoixTypeInterSalarie $choixTypeInterSalarie
     * @return JsonResponse
     * @throws JsonException
     */
    public function ajoutTypeInterSalarie(ChoixTypeInterSalarie $choixTypeInterSalarie):JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), false, 512, JSON_THROW_ON_ERROR);

         $choixTypeInterSalarie->ajouter($contenu->idInter, $contenu->idSalarie,$contenu->type);
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route("/validerHonneur")
     */
    public function validerHonneur():JsonResponse{
        $salarie = $this->getUser()->getSalarie();
        $salarie->setIsHonneur(true);
        $this->manager->persist($salarie);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @Route("/licence/{id}",name="licence")
     * @isGranted("ROLE_ENTREPRISE")
     * @param choixTemplate $choixTemplate
     * @param $id
     * @return Response
     */
    public function licenceAT(choixTemplate $choixTemplate,DefinirDate $definirDate, $id=null):Response
    {

        if ($id){
            $salarie =$this->salarieRepository->findOneBy(['id'=>$id]);
        }
        else{
            $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        }

        $templateSalarie = $choixTemplate->templateAE($salarie->getEntreprise());
        $template = $templateSalarie;
        $day = $definirDate->aujourdhui();


        return $this->render('continuer_inscription_otd/licence.html.twig', [
            'template' => $template,
            'salarie' => $salarie,
            'limite'=>$day->format('Y-m-d')
        ]);
    }

    /**
     * @Route("/Enregistrerlicence")
     * @isGranted("ROLE_SALARIE")
     *
     * @return JsonResponse
     */
    public function licence():JsonResponse
    {
        $date = DateTime::createFromFormat('Y-m-j', $this->request->get('validite'));
        $Fichier = $this->request->files->get('fichier');
        $numero = $this->request->get('numero');
        $fichierJustificatif = $this->request->files->get('justificatif');
        $identifiant = $this->request->get('identifiant');
        $password = $this->request->get('password');
        $salarie = $this->salarieRepository->findOneBy(['id' => $this->request->get('salarie')]);
        $dossier = $this->getParameter('licence_directory');
        $dossierJustificatif = $this->getParameter('justificatif_directory');
        $nomFichier = 'licence' . $salarie->getCivilite()->getPrenom() . $salarie->getCivilite()->getNom() . '.' . $Fichier->guessExtension();
        $nomValidite = 'justificatif' . $salarie->getCivilite()->getPrenom() . $salarie->getCivilite()->getNom() . '.' . $fichierJustificatif->guessExtension();
        $Fichier->move($dossier, $nomFichier);
        $fichierJustificatif->move($dossierJustificatif, $nomValidite);
        $licence = new LicenceDgac();
        $licence->setFichierLicence($nomFichier)
            ->setNumeroDeLicence($numero)
        ->setExploitant('FRA'.$this->request->get('exploitant'));

        $alpha = new AlphaTango();
        if ($identifiant && $password) {

            $alpha->setIdentifiant($identifiant)
                ->setMotDePasse($password);
        }
        $alpha->setFinValidite($date)
            ->setAttestationFormation($nomValidite);
        $salarie->setLicenceDgac($licence)
            ->setAlphaTango($alpha);
        $this->manager->persist($alpha);
        $this->manager->persist($licence);
        $this->manager->persist($salarie);
        $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse;
    }

    /**
     * @Route("/removeTypeInterSalarie")
     * @isGranted("ROLE_SALARIE")
     *
     * @param ChoixTypeInterSalarie $choixTypeInterSalarie
     * @return JsonResponse
     * @throws JsonException
     */
    public function removeTypeInterSalarie(ChoixTypeInterSalarie $choixTypeInterSalarie):JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $choixTypeInterSalarie->retirer($contenu->idInter, $contenu->idSalarie,$contenu->type);

        return new JsonResponse();
    }

    /**
     * @Route("/alphaTango")
     * @isGranted("ROLE_SALARIE")
     * @return JsonResponse
     */
    public function alphaTango():JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $salarie = $this->salarieRepository->findOneBy(['id' => $contenu['salarie']]);
        if (!$salarie->getAlphaTango()) {
            $alpha = new AlphaTango();
            $alpha->setSalarie($salarie);
        } else {
            $alpha = $salarie->getAlphaTango();
        }
        $alpha->setIdentifiant($contenu['identifiant'])
            ->setMotDePasse($contenu['password']);
        $this->manager->persist($alpha);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @Route("/distanceInter")
     * @isGranted("ROLE_SALARIE")
     * @param Geoloc $geoloc
     * @return JsonResponse
     * @throws JsonException
     */
    public function distanceInter(Geoloc $geoloc):JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $id = $contenu['idsalarie'];
        $OTD = $this->salarieRepository->find($id);
        $lat = $OTD->getAdresse()->getCoordonnees()->getLatitude();
        $lon = $OTD->getAdresse()->getCoordonnees()->getLongitude();
        $distanceInter = $geoloc->distance($lat, $lon, (int)$contenu['distance']);
        $OTD->setPeriInter($contenu['distance']);
        $OTD->getAdresse()->getCoordonnees()->setLatMinInter($distanceInter[0])
            ->setLatMaxInter($distanceInter[1])
            ->setLonMinInter($distanceInter[2])
            ->setLonMaxInter($distanceInter[3]);
        $this->manager->persist($OTD);
        $this->manager->flush();
        return new JsonResponse();
    }


    /**
     * @param DemandeurRepository $demandeurRepository
     * @param DefinirDate $definirDate
     * @param PourcentageRepository $repository
     * @param MangoPayService $mangoPayService
     * @param PackSupRepository $supRepository
     * @return Response
     * @throws NonUniqueResultException
     * @Route("/conditions générales",name="terminerInscription")
     * @Security("is_granted('ROLE_ENTREPRISE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_BTP') or is_granted('ROLE_CONSULTANT') or is_granted('ROLE_INSTITUTION') or is_granted('ROLE_GRANDCOMPTE')")
     */
    public function terminerInscription(DemandeurRepository $demandeurRepository,
                                        DefinirDate $definirDate,PourcentageRepository $repository,
                                        MangoPayService $mangoPayService,PackSupRepository $supRepository):Response
    {
        $user = $this->getUser();
        $pack =null;
        $demandeur = $demandeurRepository->findOneBy(['user'=>$user]);
        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);
        $agent = $this->agentRepository->findOneBy(['user'=>$user]);
        $banque =new Banque();
        $banque->setActif(true);
        $form = $this->createForm(BanqueType::class,$banque);
        $form->handleRequest($this->request);
        if ($salarie){
            $etat = $this->etatAbonnementRepository->trouverEtat($salarie->getEntreprise(),$definirDate->aujourdhui());
            $dureeOtd = $etat->getDatefin()->diff($etat->getDateDebut())->format('%m%');
            $inscrit = $salarie->getEntreprise();



        }
        elseif($agent){
            $etat = $this->aboTotalInstiRepository->findAbonnement($agent->getDemandeur(),$definirDate->aujourdhuiImmutable());

            $dureeOtd = 12;
            $inscrit = $agent->getDemandeur();

            if ($user->hasRole('ROLE_INSTITUTION')){
                $pack = $supRepository->findByHabitant($etat[0]->getAbonnement()->getProfil(),$etat[0]->getAbonnement()->getLimiteH(),$etat[0]->getAbonnement()->getLimiteB());
            }else{
                $pack = $supRepository->findBy(['cible'=>'gc','profil'=>$agent->getDemandeur()->getProfil()]);
            }

        }
        else{
            $etat=null;
            $dureeOtd = null;
        }
        $cerfa = $repository->findOneBy(['nom'=>'cerfa']);
        if ($form->isSubmitted() && $form->isValid()){
            $inscrit->addBanque($banque)
                ->setCgv(true);
            try {
                if ($inscrit instanceof Entreprise){
                    $bank = $mangoPayService->createBanqueIban(
                        $this->getUser()->getMangoPayId(),
                        $inscrit->getDenomination(),
                        $inscrit->getAdresse(),
                        $banque
                    );

                    $user->setBankMangoPay($bank->Id);
                    $this->manager->persist($user);

                }
                $this->manager->persist($inscrit);

            }
            catch (\Exception $th){

            }
            $this->manager->persist($banque);
            $this->manager->flush();
            return $this->redirectToRoute('sepa');
        }


        return $this->render('continuer_inscription_otd/terminerInscription.html.twig', [
            'user' => $user,
            'demandeur'=>$demandeur,
            'salarie'=>$salarie,
            'form'=>$form->createView(),
            'etat'=>$etat,
            'cerfa'=>$cerfa,
            'dureeOtd'=>$dureeOtd,
            'packs'=>$pack
        ]);
    }

    /**
     * @Route("/accordCgu",name="accordCgv")
     * @Security("is_granted('ROLE_ENTREPRISE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_INSTITUTION') or is_granted('ROLE_GRANDCOMPTE') or is_granted('ROLE_CONSULTANT')")
     * @param DemandeurRepository $demandeurRepository
     * @return RedirectResponse
     */
    public function accordCgv(DemandeurRepository $demandeurRepository,FacturePdfService $facturePdfService):RedirectResponse
    {

        $user = $this->getUser();

        $demandeur = $demandeurRepository->findOneBy(['user'=>$user]);
        $agent  = $this->agentRepository->findOneBy(['user'=>$user]);

        if ($demandeur){
            $demandeur->setCgv(true);
            $this->manager->persist($demandeur);
            $this->manager->flush();
            return $this->redirectToRoute('demandeur');
        }
        elseif ($agent){
            $etat = $this->aboTotalInstiRepository->findOneBy(['demandeur'=>$agent->getDemandeur(),'abonne'=>true]);
            $facturePdfService->factureAbonnementGci($agent,$etat);
            return $this->redirectToRoute('homeInsti',['code'=>null]);
        }
    }

    /**
     * @Route("/sepa",name="sepa")
     * @Security("is_granted('ROLE_GRANDCOMPTE') and is_granted('ROLE_MANITOU') or is_granted('ROLE_ENTREPRISE') or is_granted('ROLE_BTP')")
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     * @throws NonUniqueResultException
     */
    public function sepa(choixTemplate $choixTemplate, DefinirDate $definirDate,BanqueRepository $banqueRepository)
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $date = $definirDate->aujourdhui();
        if ($salarie){
            $debiteur = $salarie->getEntreprise();
           // $etat = $this->etatAbonnementRepository->trouverEtat($debiteur, $date);
           // $debut = $etat->getDateDebut();
            //$fin = $etat->getDatefin();
            $nom = $debiteur->getDenomination();
            $formJ = $debiteur->getFormJuridique();
            $banque = $banqueRepository->findOneBy(['entreprise'=>$salarie->getEntreprise(),'actif'=>true]);
        }
        else{
            $debiteur = $agent->getDemandeur();
            //$etat = $this->aboTotalInstiRepository->findAbonnement($debiteur,$definirDate->aujourdhuiImmutable());
           $nom = $debiteur->getNom();
            //$debut = $etat->getDebut();
            //$fin = $etat->getFin();
            $formJ = 'gc';
            $banque = $banqueRepository->findOneBy(['institution'=>$agent->getDemandeur(),'actif'=>true]);
        }
        $lieu = $debiteur->getAdresse()->getVille();
        $template = $choixTemplate->templateSepa($this->getUser());
        return $this->render('entreprise/sepa.html.twig', [
            'entreprise' => $debiteur,
            'banque'=>$banque,

            'date' => $date,
            //'debut'=>$debut,
           // 'fin'=>$fin,
            'nom'=>$nom,
            'formJuridique'=>$formJ,
            'lieu'=>$lieu,
            'template'=>$template

        ]);
    }

    /**
     * @return Response
     * @Route ("/enAttenteduMandatDePrelevement",name="enAttenteDuMandat")
     */
    public function attenteMandat():Response{

        return $this->render('entreprise/attenteMandat.html.twig');
    }

    /**
     * @return Response
     * @Route("/instruction-continuer-inscription",name="alerteContinuerInscription")
     */
    public function alerteContinuerInscription():Response{
        $salarie = $this->getUser()->getSalarie();
        $entreprise = $salarie->getEntreprise();
        return $this->render("continuer_inscription_otd/alerte.html.twig",[
            'entreprise'=>$entreprise,
            'salarie'=>$salarie,

        ]);
    }

    /**
     * @param MissionRepository $missionRepository
     * @param \App\Service\choixTemplate $choixTemplate
     * @param int|null $id
     * @return Response
     * @Route("/entreprise/rappelInter/{id}",name="rappelInter")
     */
    public function rappelInter(MissionRepository $missionRepository,choixTemplate $choixTemplate,int $id=null):Response{
        if ($id){
            $salarie = $this->salarieRepository->find($id);
        }
        else{
            $salarie = $this->getUser()->getSalarie();
        }
        $missions = $missionRepository->findBySalarie($salarie);
        $templateSalarie = $choixTemplate->templateAE($this->getUser()->getSalarie()->getEntreprise());
         return $this->render('entreprise/rappelInterOdi.html.twig',[
             'missions'=>$missions,
             'template'=>$templateSalarie,
             'id'=>$id
         ]);
    }
}
