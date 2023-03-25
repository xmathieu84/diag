<?php

namespace App\Controller;

use App\Entity\MangoPayIn;
use App\Entity\Paiement;

use App\Form\NavigateurType;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\ReservationRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\DemandeurRepository;
use App\Repository\InterDiagRepository;
use App\Service\FactureCommisionInter;
use App\Service\PropChoix;
use DateTime;
use DateTimeZone;
use App\Form\PaiementType;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AbonnementsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Mpdf\MpdfException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\InterventionRepository;
use App\Repository\PaiementRepository;
use App\Repository\PourcentageRepository;
use App\Service\choixTemplate;
use App\Service\codeActivation;
use App\Service\DefinirDate;
use App\Service\Mail;
use App\Service\MangoPayService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class PaiementController extends AbstractController
{
    use RequestTrait, EntityManagerTrait, InterRepoTrait,ReservationRepoTrait,EntrepriseRepoTrait,SalarieRepoTrait;
    private \Symfony\Component\Form\FormFactoryInterface $formFactory;
    public function __construct(\Symfony\Component\Form\FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/paiement/{type}/{id}",name="paiement")
     *
     * @isGranted("ROLE_DEMANDEUR")
     *
     * Etape 1 du paiement. Mise en place du formulaire
     * Etape 3 du paiement. Validation du paiement.
     *
     * @param InterventionRepository $interventionRepository
     * @param $id
     * @param $type
     * @param Request $request
     * @param DemandeurRepository $demandeurRepository
     * @return Response
     */
    public function paiement(
        InterventionRepository $interventionRepository,
        $id =null,
        $type,
        Request $request,
        DemandeurRepository $demandeurRepository
    ): Response {
        $intervention = $interventionRepository->findOneBy(['id' => $id]);
        $demandeur = $demandeurRepository->findOneBy(['user'=>$this->getUser()]);

        $form = $this->formFactory->createNamed('', PaiementType::class, [
            'action' => ' ',
            'method' => 'post'
        ]);
        if ($type === 'acompte') {
            $montant = $intervention->getAcommpte();

        }
        if ($type === 'intervention') {
            $montant = $intervention->getPrix() - $intervention->getAcommpte();

        }

        $url = 'http' . (($request->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $url .= substr($request->server->get('REQUEST_URI'), 0, strrpos($request->server->get('REQUEST_URI'), '/') + 1);
        $url .= $id;


        if ($request->query->get('data')) {

            return $this->redirectToRoute('traitementPaiement',[
                'data'=>$request->query->get('data'),
                'idInter'=>$id,
                'type'=>$type,

            ]);

        }
        return $this->render('paiement/acompte.html.twig', [
            'demandeur' => $demandeur,
            'intervention' => $intervention,
            'form' => $form->createView(),
            'url' => $url,
            'type' => $type,
            'montant' => $montant,

        ]);
    }

    /**
     * @Route("/traitementPaiement/{data}/{idInter}/{type}",name="traitementPaiement")
     * @Route("/traitementPaiementDiag/{data}/{uuid}/{type}",name="traitementPaiementDiag")
     * @param string $data
     * @param int|null $idInter
     * @param string $type
     * @param string|null $uuid
     * @return Response
     */
    public function infoNavigateur(string $data,string $type,string $uuid=null,int $idInter=null):Response{

        return $this->render('paiement/traitementPaiement.html.twig',[
            'data'=>$data,
            'idInter'=>$idInter,
            'type'=>$type,
            'uuid'=>$uuid

        ]);
    }

    /**
     * @Route("/validerPaiement")
     *
     * @param MangoPayService $mangoPayService
     * @param PourcentageRepository $pourcentageRepository
     * @return JsonResponse
     */
    public function validerPaiement(MangoPayService $mangoPayService,InterDiagRepository $interDiagRepository){


        $content = json_decode($this->request->getContent(),true);
        if ($content['inter'] !==""){
            $intervention = $this->interventionRepository->findOneBy(['id' => $content['inter']]);
            $entreprise = $intervention->getPropositionChoisie()->getSalarie()->getEntreprise();
        }
        else{
            $intervention = $interDiagRepository->findOneBy(['identifiat'=>$content['uuid']]);
            $entreprise = $intervention->getOdi()->getEntreprise();

        }
        $dirigeant = $this->salarieRepository->findDirirgeant($entreprise);
        $wallet = $dirigeant->getUser()->getWalletMangoID();

        $taux = $entreprise->getCommission();
        $retourAdresse = 'http' . (($this->request->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $retourAdresse .= '/resultatTransaction/';
        $retourAdresse .= $content['typeInter'];
        if ($content['inter'] !==""){
            $retourAdresse .= '/' . $intervention->getId() . '/';
        }
        else{
            $retourAdresse .= '/' . $intervention->getIdentifiat() . '/';
        }


        $cardId = $this->getUser()->getCardId();
        if ($content['typeInter']=== 'acompte') {
            if ($content['inter']!==""){
                $montant = $intervention->getAcommpte();
                $com = $taux * $intervention->getPrix() / 100;
            }
            else{
                $montant = $intervention->getAcompte();
                $com = $taux * $intervention->getPrix() / 100;
            }

        }
        if ($content['typeInter'] === 'intervention') {
            if ($content['inter'] !==""){
                $montant = $intervention->getPrix() - $intervention->getAcommpte();
            }else{
                $montant = $intervention->getPrix() - $intervention->getAcompte();
            }

            $com = 0;
        }
        $cardUpdate = $mangoPayService->validateCard($cardId, $content['data']);
        try {
            $payIn = $mangoPayService->createDirectPayIn($this->request,$wallet,
                $cardUpdate, $montant, $com, $retourAdresse,$content);
        }catch (\Exception $e){

        }


        return (new JsonResponse())->setData($payIn->ExecutionDetails->SecureModeRedirectURL);

    }

    /**
     * @Route("/creerCarte")
     * @isGranted("ROLE_DEMANDEUR")
     * Etape 2 du paiement. Création de la carte de paiement chez mangoPay
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     */
    public function creationCarte(MangoPayService $mangoPayService): JsonResponse
    {
        $idUser = $this->getUser()->getMangoPayId();
        $type = $this->request->getContent();
        $card = $mangoPayService->createCard($idUser, $type);
        $user = $this->getUser()->setCardId($card->Id);
        $this->manager->persist($user);
        $this->manager->flush();
        return (new JsonResponse())->setData([
            'carte'=>$card
        ]);
    }

    /**
     * @param MangoPayService $mangoPayService
     * @param $id
     * @param $type
     * @return RedirectResponse
     * @Route("/resultatTransaction/{id}/{type}",name="resultatTransaction")
     */
    public function resultatTransaction(MangoPayService $mangoPayService,$id,$type){
        $result = $mangoPayService->viewPayIn($this->request->get('transactionId'));
        if ($result->Status ==="FAILED"){
            return $this->redirectToRoute('echecCarte');
        }else{
            return $this->redirectToRoute('validerCarte',[
                'id'=>$id,
                'type'=>$type
            ]);
        }
    }

    /**
     * @Route("/validerCarte/{id}/{type}",name="validerCarte")
     * @isGranted("ROLE_DEMANDEUR")
     * @param string $type
     * @param integer $id
     * @param DefinirDate $definirDate
     * @param FactureCommisionInter $factCom
     * @param PropChoix $propChoix
     * @return Response
     * @throws NonUniqueResultException
     * @throws MpdfException
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function validerCarte($type, $id, DefinirDate $definirDate,
                                 FactureCommisionInter $factCom,PropChoix $propChoix,InterDiagRepository $interDiagRepository): Response
    {
        $date = $definirDate->aujourdhui();
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        if ($intervention){
            $reservation = $this->reservationRepository->findOneBy(['intervention'=>$intervention]);
            $wallet = $reservation->getSalarie()->getEntreprise()->getSalaries()[0]->getUser()->getWalletMangoId();
            $intervention->setStatuInter('Intervention validée');
            $typeInter = 'drone';
        }else{
            $intervention = $interDiagRepository->findOneBy(['identifiat'=>$id]);
           $wallet = $intervention->getOdi()->getEntreprise()->getSalaries()[0]->getUser()->getWalletMangoID();
            $intervention->setStatut('Intervention validée');
            $typeInter = 'diag';

        }
        if ($type === 'acompte') {
            $this->manager->persist($intervention);
            $factCom->comInter($intervention,$type);
            $this->manager->flush();
            if ($typeInter==='drone'){
                $propChoix->traitementProposition($intervention,$intervention->getPropositionChoisie());
                $montant = $intervention->getAcommpte();
            }else{
                $montant = $intervention->getAcompte();
            }


        } else {
            if ($typeInter==="drone"){
                $montant = $intervention->getPrix() - $intervention->getAcommpte();
            }
            else{
                $montant = $intervention->getPrix() - $intervention->getAcompte();
            }

            $intervention->setDatePaiement($date);
            $this->manager->persist($intervention);
        }
        $mangoPayIn = new MangoPayIn();
        if ($typeInter ==="drone"){
            $mangoPayIn->setIntervention($intervention);
        }
        else{
            $mangoPayIn->setInterDiag($intervention);
        }

        $user = $this->getUser();
        $mangoPayIn->setWalletId($wallet)
            ->setDate($date)
            ->setMontant($montant)
            ->setType($type);
        $this->manager->persist($mangoPayIn);
        $this->manager->flush();

        return $this->render('paiement/paiementAccepte.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @return Response
     * @Route("/echec-carte",name="echecCarte")
     */
    public function echecCarte(){
        return $this->render('paiement/echecPaiement.html.twig');
    }



    /**
     * @Route("/creerVirement")
     * @Security("is_granted('ROLE_SALARIE') or is_granted('ROLE_DEMANDEUR')")
     * Création d'un paiement par virement bancaire d'un demandeur ou d'une entreprise
     * @param MangoPayService $mangoPayService
     * @param PourcentageRepository $pourcentageRepository
     * @return JsonResponse
     */
    public function creerVirement(MangoPayService $mangoPayService, InterDiagRepository $interDiagRepository): JsonResponse
    {
        $idWallet = $this->getUser()->getWalletMangoId();
        $iddemandeur = $this->getUser()->getMangoPayId();

        $content = json_decode($this->request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($content['interType']==='drone'){
            $intervention = $this->interventionRepository->findOneBy(['id' => $content['idInter']]);
            $taux = $intervention->getReservation()->getSalarie()->getEntreprise()->getCommission();
            if ($content['typeInter'] === 'acompte') {
                $montant = $intervention->getAcommpte();
                $com = $taux * $intervention->getPrix() / 100;
            }
            if ($content['typeInter'] === 'intervention') {
                $montant = $intervention->getPrix() - $intervention->getAcommpte();
                $com = 0;
            }
        }
        else{
            $intervention =$interDiagRepository->findOneBy(['identifiat' => $content['idInter']]);
            $taux = $intervention->getOdi()->getEntreprise()->getCommission();
            if ($content['typeInter'] === 'acompte') {
                $montant = $intervention->getAcompte();
                $com = $taux * $intervention->getPrix() / 100;
            }
            if ($content['typeInter'] === 'intervention') {
                $montant = $intervention->getPrix() - $intervention->getAcompte();
                $com = 0;
            }
        }

        $virement = $mangoPayService->creatBankWireDirectPayIn($iddemandeur, $idWallet, $montant, $com);
        return (new JsonResponse())->setData($virement);


    }

    /**
     * @Route("/validerModePaiement/{type}-{id}",name="validerModePaiement")
     *
     * @param string $type
     * @param integer $id
     * @param DefinirDate $definirDate
     * @return RedirectResponse
     * @throws Exception
     */
    public function validerModePaiement($type, $id, DefinirDate $definirDate): RedirectResponse
    {
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        $salarie = $intervention->getReservation()->getSalarie();
        $wallet = $salarie->getEntreprise()->getSalaries()[0]->getUser()->getWalletMangoId();
        if ($type === 'acompte') {
            $montant = $intervention->getAcommpte();
        }
        if ($type === 'intervention') {
            $montant = $intervention->getPrix() - $intervention->getAcommpte();
        }
        $date = $definirDate->aujourdhui();
        $mangoPayIn = new MangoPayIn();

        $mangoPayIn->setType($type)
            ->setMontant($montant)
            ->setDate($date)
            ->setIntervention($intervention)
            ->setWalletId($wallet);
        $intervention->setStatuInter('en attente de virement');
        $this->manager->persist($intervention);
        $this->manager->persist($mangoPayIn);
        $this->manager->flush();
        return $this->redirectToRoute('paiementEnAttente');
    }
    /**
     * @Route("paiementEnAttente",name="paiementEnAttente")
     *
     * @return Response
     */
    public function indicationPaiement(): Response
    {
        $user = $this->getUser();
        return $this->render('paiement/indicationPaiement.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Route("/recevoir/paiement",name="recevoirPaiement")
     * @Security("is_granted('ROLE_SALARIE') and is_granted('ROLE_CLASSIC')")
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function recevoirPaiement(choixTemplate $choixTemplate)
    {
        $user = $this->getUser();
        $template = $choixTemplate->templateSalEnt($user);

        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $this->entrepriseRepository->findOneBy(['user'=>$this->getUser()]);
        if ($entreprise) {
            $profil = 'auto-entrepreneur';
        } elseif ($salarie) {
            $profil = 'salarie';
        }

        return $this->render('paiement/recevoirPaiement.html.twig', [
            'template' => $template,
            'profil' => $profil
        ]);
    }

    /**
     * @Route("/enregistrerPaiement")
     *@isGranted("ROLE_SALARIE")
     * @param codeActivation $codeActivation
     * @return void
     */
    public function enregistrerPaiement(codeActivation $codeActivation, Mail $mail): JsonResponse
    {
        $lien = $codeActivation->generer();

        $requete = json_decode($this->request->getContent(), true);
        $montant = $requete['montant'];
        $user = $this->getUser();
        $paiement = new Paiement();
        $paiement->setUtilisateur($user)
            ->setCode($lien)
            ->setMontant($montant)
            ->setType('Paiement intervention hors diag-drone');
        $this->manager->persist($paiement);
        $this->manager->flush();
        $email = $mail->mailPaiement($requete['email'], $requete['montant'], $lien);

        $reponse = new JsonResponse();
        return $reponse->setData(['home' => '/entreprise']);
    }

    /**
     * @Route("/effectuerPaiement/{code}",name="effectuerPaiement")
     * 
     * @param string $code
     * @param PaiementRepository $paiementRepository
     * @param MangoPayService $mangoPayService
     * @return Response
     */
    public function effectuerPaiement($code, PaiementRepository $paiementRepository, MangoPayService $mangoPayService, DefinirDate $definirDate): Response
    {

        $paiement = $paiementRepository->findOneBy(['code' => $code]);



        $form = $this->formFactory->createNamed('', PaiementType::class, [
            'action' => 'https://homologation-webpayment.payline.com/webpayment/getToken',
            'method' => 'post'
        ]);
        $url = 'http' . (($this->request->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $url .= substr($this->request->server->get('REQUEST_URI'), 0, strripos($this->request->server->get('REQUEST_URI'), '/') + 1);
        $url .= $code;

        if ($this->request->query->get('data')) {


                $codeRegistration = $this->request->query->get('data');
        return $this->redirectToRoute('validatePaiementHdd',[
            'data'=>$codeRegistration,
            'id'=>$paiement->getId()
        ]);
                /*$retourAdresse = 'http' . (($this->request->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
                $retourAdresse .= '/paiementEffectue';
                $retourAdresse .= '/' . $code;
                $cardId = $paiement->getCardId();
                $cardUpdate = $mangoPayService->validateCard($cardId, $codeRegistration);
                $payIn = $mangoPayService->createDirectPayIn($wallet, $cardUpdate, $paiement->getMontant(), 0, $retourAdresse);
                $paiement->setCardId($cardUpdate->CardId);

                $mangoPayIn->setWalletId($wallet)
                    ->setDate($date)
                    ->setIntervention(null)
                    ->setMontant($paiement->getMontant())
                    ->setType('intervention');
                $this->manager->persist($mangoPayIn);
                $this->manager->persist($paiement);
                $this->manager->flush();

                return $this->redirect($payIn->ExecutionDetails->SecureModeRedirectURL);*/
            }





        return $this->render('paiement/effectuerPaiement.html.twig', [
            'form' => $form->createView(),
            'paiement' => $paiement,
            'url' => $url
        ]);
    }

    /**
     * @param $data
     * @param $id
     * @return Response
     * @Route("/validatePaiementHdd/{data}/{id}",name="validatePaiementHdd")
     */
    public function validatePaiementHdd($data,$id){
        return $this->render('paiement/traitementPaiementHdd.html.twig',[
            'data'=>$data,
            'id'=>$id
        ]);
    }

    /**
     * @Route("/terminerPaiementHdd")
     */
    public function terminerPaimementHdd(DefinirDate $definirDate,PaiementRepository $paiementRepository,MangoPayService $mangoPayService){
        $content  = json_decode($this->request->getContent(),true);
        $paiement = $paiementRepository->findOneBy(['id'=>$content['paiement']]);

        $mangoPayIn = new MangoPayIn();
        $date = $definirDate->aujourdhui();
        $retourAdresse = 'http' . (($this->request->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
                $retourAdresse .= '/paiementEffectue';
                $retourAdresse .= '/' . $paiement->getCode();
                $cardId = $paiement->getCardId();
                $cardUpdate = $mangoPayService->validateCard($cardId, $content['data']);
                $payIn = $mangoPayService->createDirectPayIn($this->request,$paiement->getUtilisateur()->getWalletMangoID(), $cardUpdate, $paiement->getMontant(), 0, $retourAdresse,$content);
                $paiement->setCardId($cardUpdate->CardId);

                $mangoPayIn->setWalletId($paiement->getUtilisateur()->getWalletMangoID())
                    ->setDate($date)
                    ->setIntervention(null)
                    ->setMontant($paiement->getMontant())
                    ->setType('intervention');
                $this->manager->persist($mangoPayIn);
                $this->manager->persist($paiement);
                $this->manager->flush();


       return new JsonResponse($payIn->ExecutionDetails->SecureModeRedirectURL);
    }


    /**
     * @Route("/createMangoUser")
     * 
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     */
    public function createMangoUser(MangoPayService $mangoPayService): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $mangoUser = $mangoPayService->getMangoUserHdd($contenu['name'], $contenu['firstName'], $contenu['mail']);

        $response = new JsonResponse();
        return $response->setData($mangoUser);
    }



    /**
     * @Route("/createCardHdd")
     * 
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     */
    public function createCardHdd(MangoPayService $mangoPayService, PaiementRepository $paiementRepository): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $card = $mangoPayService->createCard($contenu['user'], $contenu['carte']);
        $paiement = $paiementRepository->findOneBy(['id' => $contenu['idPaiement']]);
        $paiement->setCardId($card->Id);
        $this->manager->persist($paiement);
        $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse->setData($card);
    }

    /**
     * @Route("/paiementEffectue/{code}")
     *
     * @param string $code
     * @param PaiementRepository $paiementRepository
     * @return Response
     */
    public function paiementEffectue($code, PaiementRepository $paiementRepository, MangoPayService $mangoPayService): Response
    {
        $paiement = $paiementRepository->findOneBy(['code' => $code]);

        $cardId = $paiement->getCardId();

        $mangoPayService->deactivateCard($cardId);

        $paiement->setCode(null);
        $this->manager->persist($paiement);
        $this->manager->flush();

        return $this->render('paiement/carteValidee.html.twig');
    }

    /**
     * @Route("/paiements/institution/{id}",name="paiementInsti")
     *  @isGranted("ROLE_INSTITUTION")
     * @param MangoPayService $mangoPayService
     * @param integer $id
     * @param PourcentageRepository $pourcentageRepository
     * @return Response
     */
    public function paiementInstitutionnel(MangoPayService $mangoPayService, $id, PourcentageRepository $pourcentageRepository)
    {
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        $pourcentage = $pourcentageRepository->findOneBy(['nom' => 'commission']);
        $user = $this->getUser();
        $paiement = $mangoPayService->creatBankWireDirectPayIn(
            $user->getMangoPayid(),
            $user->getWalletMangoId(),
            $intervention->getPrix(),
            $intervention->getPrix() * $pourcentage->getTaux()
        );


        return $this->render('paiement/paiementInstitutionnel.html.twig', [
            'user' => $user,
            'paiement' => $paiement,
            'idInter' => $id
        ]);
    }

    /**
     * @Route("/insti/{id}",name="insti")
     * @isGranted("ROLE_INSTITUTION")
     * @param [type] $id
     * @return RedirectResponse
     */
    public function validerVirementInstitutionnel($id, DefinirDate $definirDate): RedirectResponse
    {
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        $intervention->setStatuInter('en attente de virement');
        $wallet = $intervention->getReservation()->getSalarie()->getEntreprise()->getUser()->getWalletMangoId();
        $payIn = new MangoPayIn();
        $payIn->setIntervention($intervention)
            ->setMontant($intervention->getPrix())
            ->setDate($definirDate->aujourdhui())
            ->setType('intevention')
            ->setWalletId($wallet);
        $this->manager->persist($payIn);
        $this->manager->persist($intervention);
        $this->manager->flush();

        return $this->redirectToRoute('demandeur_diag');
    }
    /**
     * @Route("/paiementCerfa/{entreprise}-{intervention}",name="paiementCerfa")
     * @isGranted("ROLE_SALARIE")
     * Paiement de l'envoie du cerfa
     * @param Request $request
     * @param integer $entreprise
     * @param integer $intervention
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     */
    public function paiementCerfa(choixTemplate $choixTemplate, MangoPayService $mangoPayService, Request $request, $entreprise, $intervention): Response
    {
        $prix = 35;
        $user = $this->getUser();
        $template = $choixTemplate->templateSalEnt($user);
        $form = $this->formFactory->createNamed('', PaiementType::class, [
            'action' => 'https://homologation-webpayment.payline.com/webpayment/getToken',
            'method' => 'post'
        ]);
        $url = 'http' . (($request->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $url .= substr($request->server->get('REQUEST_URI'), 0, strripos($request->server->get('REQUEST_URI'), '/') + 1);
        $url .= $entreprise . '-' . $intervention;

        if ($request->query->get('data')) {

            try {

                $codeRegistration = $request->query->get('data');

                $retourAdresse = 'http' . (($request->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
                $retourAdresse .= '/validerCarte/cerfa';
                $cardId = $this->getUser()->getCardId();
                $cardUpdate = $mangoPayService->validateCard($cardId, $codeRegistration);
                $payIn = $mangoPayService->createDirectPayIn('95908834', $cardUpdate, $prix, 0, $retourAdresse);

                return $this->redirect($payIn->ExecutionDetails->SecureModeRedirectURL);
            } catch (\Throwable $th) {
                
            }
        }

        return $this->render('paiement/paiementCerfa.html.twig', [
            'form' => $form->createView(),
            'prix' => $prix,
            'template' => $template,
            'intervention' => $intervention,
            'url' => null
        ]);
    }
    /**
     * @Route("/recupererWallet")
     * @isGranted("ROLE_SALARIE")
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     */
    public function recupererWallett(MangoPayService $mangoPayService): JsonResponse
    {
        $idWallet = $this->getUser()->getSalarie()->getEntreprise()->getUser()->getWalletMangoId();
        $wallet = $mangoPayService->oneWallet($idWallet);
        $response = new JsonResponse();
        return $response->setData($wallet);
    }
    /**
     * @Route("/creationCarteCerfa")
     * @isGranted("ROLE_SALARIE")
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     */
    public function creationCarteCerfa(MangoPayService $mangoPayService): JsonResponse
    {
        $idUser = $this->getUser()->getSalarie()->getEntreprise()->getUser()->getMangoPayId();
        $type = $this->request->getContent();
        $card = $mangoPayService->createCard($idUser, $type);
        $user = $this->getUser()->setCardId($card->Id);
        $this->manager->persist($user);
        $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse->setData($card);
    }
    /**
     * @Route("/validerPaimentByWallet",name="validerPaimentByWallet")
     *
     * @param MangoPayService $mangoPayService
     * @return RedirectResponse
     */
    public function validerPaimentByWallet(MangoPayService $mangoPayService): RedirectResponse
    {
        $user = $this->getUser()->getSalarie()->getEntreprise()->getUser();
        $transfert = $mangoPayService->PayWithWallet($user);
        return $this->redirectToRoute('validerCarteCerfa');
    }
    /**
     * @Route("/validerCarteCerfa",name="validerCarteCerfa")
     * @isGranted("ROLE_SALARIE")
     * 
     *
     * 
     * @param DefinirDate $definirDate
     * @return Response
     */
    public function validerCarteCerfa(DefinirDate $definirDate, choixTemplate $choixTemplate): Response
    {
        $user = $this->getUser();
        $wallet = $user->getSalarie()->getEntreprise()->getUser()->getWalletMangoId();
        $date = $definirDate->aujourdhui();
        $template = $choixTemplate->templateSalEnt($user);
        $mangoPayIn = new MangoPayIn();
        $user = $this->getUser();
        $mangoPayIn->setWalletId($wallet)
            ->setDate($date)
            ->setIntervention(null)
            ->setMontant(35)
            ->setType('cerfa');
        $this->manager->persist($mangoPayIn);
        $this->manager->flush();

        return $this->render('paiement/paiementCerfaAccepte.html.twig', [
            'template' => $template,
            'user' => $user
        ]);
    }
}
