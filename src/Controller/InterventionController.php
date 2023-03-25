<?php

namespace App\Controller;


use App\Entity\BudgetInter;
use App\Entity\Photo;
use App\Event\AdressEvent;
use App\Form\AdresseInterType;
use App\Helper\AgentRepoTrait;
use App\Repository\FichierOTDRepository;
use App\Service\InterConcurrence;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Event\ApiMatchSalarieEvent;
use App\Repository\DemandeurRepository;
use App\Service\Mail;
use App\Entity\Adresse;
use App\Entity\Rapport;
use App\Service\Geoloc;
use App\Service\Fichier;
use App\Form\AdresseType;
use App\Entity\Coordonnees;
use App\Entity\Intervention;
use App\Entity\Proposition;
use App\Entity\Reservation;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use App\Form\InterEtape3Type;

use App\Form\InterventionType;
use App\Helper\InterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\ListeInterRepoTrait;


use App\Helper\ReservationRepoTrait;


use App\Helper\ListeInterTypeInterRepoTrait;
use App\Helper\TauxHoraireRepoTrait;
use App\Helper\TypeInterRepoTrait;
use App\Repository\MailPrefectureRepository;
use App\Service\choixTemplate;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InterventionController
 * @package App\Controller
 *
 */
class InterventionController extends AbstractController
{
    use EntityManagerTrait,
        RequestTrait,
        InterRepoTrait,
        TauxHoraireRepoTrait,
        ReservationRepoTrait,
        SalarieRepoTrait,
        ListeInterRepoTrait,
        AgentRepoTrait,
        ListeInterTypeInterRepoTrait,
        TypeInterRepoTrait;


    /**
     * @Route("/intervention/{code}", name="new_inter")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     *
     * @param mixed $id
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function newInter( choixTemplate $choixTemplate,$code=null): Response
    {
        $Listeinter = $this->listeInterRepository->findAll();
        $template = $choixTemplate->templateDem($this->getUser());
        return $this->render('intervention/newinter.html.twig', [
            'user' => $this->getUser(),
            'listeInters' => $Listeinter,
            'template' => $template[0],
            'code'=>$code
        ]);
    }

    /**
     * @Route("/selectionListeinter")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     * @return JsonResponse
     */
    public function selectionListeInter(): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $idinter = $contenu['idInter'];
        $idListe = $contenu['idListe'];
        $typeInter = [];
        $listeInter = $this->listeInterRepository->findOneBy(['id' => $idListe]);
        $ListeInterTypeInters = $this->listeInterTypeInterRepository->findBy(['listeInter' => $listeInter]);
        foreach ($ListeInterTypeInters as $ListeInterTypeInter) {
            $typeInter[] = $ListeInterTypeInter->getTypeInter()->jsonSerialize();
        }

        if ($idinter == null) {
            $intervention = new Intervention();
        } else {
            $intervention = $this->interventionRepository->findOneBy(['id' => $idinter]);
        }
        $intervention->setListeInter($listeInter);
        $this->manager->persist($intervention);
        $this->manager->flush();

        $reponse = new JsonResponse();
        return $reponse->setData([
            'idInter' => $intervention->getId(), 'liste' => $typeInter, 'raccourci' => $listeInter->getRaccourci()
        ]);
    }

    /**
     * @Route("/selectionTypeinter")
     *@Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     * @return JsonResponse
     */
    public function selectionTypeInter(): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $intervention = $this->interventionRepository->findOneBy(['id' => $contenu['idIntervention']]);
        $typeinter = $this->typInterRepository->findOneBy(['id' => $contenu['idTypeInter']]);
        $intervention->setTypeInter($typeinter);
        $this->manager->persist($intervention);
        $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse->setData($intervention->getId());
    }

    /**
     * @Route("/etape2/{id}/{code}",name="etape2")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     * @param int $id
     * @param DefinirDate $definirDate
     * @param choixTemplate $choixTemplate
     * @return Response
     * @throws Exception
     */
    public function interphase1($id, DefinirDate $definirDate, choixTemplate $choixTemplate, $code =null): Response
    {
        $user = $this->getUser();
        $template = $choixTemplate->templateDem($user,$code);
        $maintentant = $definirDate->aujourdhui();
        $intervention = $this->interventionRepository->findOneBy(['id' =>$id]);
        if ($intervention->getReservation()){
            $reservation = $intervention->getReservation();
        }
        else{
            $reservation = new Reservation();
        }


        $demandeur = $template[1];

        $form = $this->createForm(InterventionType::class, $intervention,['profil'=>$demandeur->getProfil()]);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form['typeDeBien']->getData() === 'Autre'){
                $intervention->setTypeDeBien($form['autre']->getData());
            }
            if ($demandeur->getProfil() !=="Particulier propriétaire"){
                $intervention->setRenoncementDelaiRetract(true);
            }
            $intervention->setIntDem($demandeur)
                ->setStatuInter('Nouvelle demande')
                ->setCreatedAT($maintentant);

            $reservation->setIntervention($intervention);

            $this->manager->persist($intervention);
            $this->manager->persist($reservation);

            $this->manager->flush();

            return $this->redirectToRoute('etape3', [
                'id' => $intervention->getId(),
                'code'=>$code
            ]);
        }
        return $this->render('intervention/interphase1.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
            'template' => $template[0],
            'code'=>$code,
            'intervention'=>$intervention,
            "demandeur"=>$demandeur
        ]);
    }


    /**
     * @Route("/etape3/{id}/{code}",name="etape3")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     *
     * @param integer $id
     * @param Geoloc $geoloc
     * @param DefinirDate $definirDate
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function interphase2($id, Geoloc $geoloc, DefinirDate $definirDate, choixTemplate $choixTemplate,EventDispatcherInterface $eventDispatcher,string $code =null): Response
    {


        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        if ($intervention->getAdresse()){
            $adresse = $intervention->getAdresse();
        }
        else{
            $adresse = new Adresse();
        }
        $intervention->setAdresse($adresse);
        $template = $choixTemplate->templateDem($this->getUser());
        $form = $this->createForm(AdresseInterType::class, $adresse);
        $form->handleRequest($this->request);
        if ($this->getUser()->hasRole('ROLE_INSTITUTION')||$this->getUser()->hasRole('ROLE_GRANDCOMPTE')){
            $adressefact = $this->getUser()->getAgent()->getDemandeur()->getAdresse();
        }
        else{
            $adressefact = $this->getUser()->getDemandeur()->getAdresse();
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $localisationEvent = new GenericEvent($intervention);
            $eventDispatcher->dispatch($localisationEvent,  AdressEvent::NAME);

            $this->manager->persist($adresse);
            $this->manager->persist($intervention);

            $this->manager->flush();
            return $this->redirectToRoute('etape4', [
                'id' => $intervention->getId(),
                'code'=>$code,

            ]);
        }
        return $this->render('intervention/etape2.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
            'intervention' => $intervention,
            'code' => $code,
            'template' => $template[0],
            'adresseFact'=>$adressefact
        ]);
    }

    /**
     * @Route("/choixDepartement")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     * @param MailPrefectureRepository $departementRepo
     * @return JsonResponse
     */
    public function choixDepartement(MailPrefectureRepository $departementRepo): JsonResponse
    {
        $contenu = $this->request->getContent();
        $Contenu = json_decode($contenu, true);
        $intervention = $this->interventionRepository->findOneBy(['id' => $Contenu['idInter']]);
        $departement = $departementRepo->findOneBy(['numeroDepartement' => $Contenu['departement']]);

        $intervention->setDepartement($departement);
        $this->manager->persist($intervention);
        $this->manager->flush();
        return new JsonResponse();
    }


    /**
     * @Route("/etape4/{id}/{code}",name="etape4")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     *
     * @param int $id
     * @param Fichier $fichier
     * @param choixTemplate $choixTemplate
     * @param Mail $mail
     * @return Response
     */
    public function interphase3(int $id, Fichier $fichier, choixTemplate $choixTemplate, Mail $mail,string $code=null): Response
    {
        $template = $choixTemplate->templateDem($this->getUser());
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(InterEtape3Type::class, $intervention);
        $liti = $this->listeInterTypeInterRepository->findOneBy(['listeInter'=>$intervention->getListeInter(),'typeInter'=>$intervention->getTypeInter()]);
        $listePhoto = new ArrayCollection();
        $form->handleRequest($this->request);
        $type=null;
        $intemp = [];
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);

        if ($form->isSubmitted() && $form->isValid()) {

            $photos = $form->get('photos')->getData();
           if ($form->get('photoOnly')->getData()){
               $intervention->setNbrePhoto($form->get('nbrePhoto')->getData());
           }

            if ($form->get('videoOnly')->getData()){
                $intervention->setNbreVideo($form->get('nbreVideo')->getData());
            }
            foreach ($form['travaux']->getData() as $travaux){
                $intervention->addTravaux($travaux);
            }
            foreach ($form->get('intemperie')->getData() as $value){
                $intemp['intemperie'][]=$value;
            }
            if ($form['autreIntemp']->getData()){
                $intemp['intemperie'][] = $form['autreIntemp']->getData();

            }
            if ($form->get('dateIntemperie')->getData()){
                $intemp['date'][]= date_format($form->get('dateIntemperie')->getData(),"d/m/Y");

            }
            if ($form['autreType']->getData()){
                $type = $form['autreType']->getData();
            }
            $intervention->setTypeDemande($type)
                ->setIntemperie($intemp);
            foreach ($photos as $key=>$photo) {

                $nouveauNom = $fichier->moveFile($photo, $this->getParameter('photos_directory'),'photoDemandeur'.$key);
                $foto = new Photo();
                $foto->setNom($nouveauNom);
                $listePhoto->add($foto);
            }
            foreach ($listePhoto as $PHOTO) {
                $intervention->addPhotoInter($PHOTO);
            }
            if (!$intervention->getIntDem()->getAgents()->isEmpty()) {
                $agent = $this->agentRepository->findOneBy(['user' => $this->getUser()]);

                $mail->mailInterInsti($intervention->getIntDem()->getAgents()[0]->getUser()->getEmail(), $agent);
            }
            if ($form['besoinBudget']->getData()==='Oui'){
                if ($intervention->getBudgetInter()){
                    $budget = $intervention->getBudgetInter();
                }
                else{
                    $budget = new BudgetInter();
                    $intervention->setBudgetInter($budget);
                }
                $budget->setMontant($form["budgetInter"]['montant']->getData());

            }

           $this->manager->persist($intervention);
            $this->manager->flush();
            return $this->redirectToRoute('choixDate', [
                'id' => $intervention->getId(),
                'code'=>$code
            ]);
        }
        return $this->render('intervention/etape3.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
            'template' => $template[0],
            'code'=>$code,
            'liti'=>$liti,
            'intervention'=>$intervention
        ]);
    }

    /**
     * @Route("/intervention/choixDate/{id}/{code}", name="choixDate")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     *
     * @param integer $id
     * @param choixTemplate $choixTemplate
     * @param InterConcurrence $concurrence
     * @return Response
     *
     */
    public function choixDate(int $id,choixTemplate $choixTemplate,InterConcurrence $concurrence,string $code=null):Response
    {
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        $template = $choixTemplate->templateDem($this->getUser());
        $nombreOtd = $concurrence->concurrence($intervention);
        //$otdRapide = $concurrence->interConcurrenceRapideGlobal($intervention);

        return $this->render('intervention/choixDate.html.twig',[
            'template'=>$template,
            'intervention'=>$intervention,
            'nombre'=>$nombreOtd,
            //'otdRapide'=>$otdRapide,
            'code'=>$code
        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/newInter/nbreOtd/{id}")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     */
    public function nbreOtd(int $id,InterConcurrence $concurrence,DefinirDate $definirDate):JsonResponse{
        $inter = $this->interventionRepository->findOneBy(['id'=>$id]);
        $reponse = new JsonResponse();
        $content = $this->request->getContent();
        $date = new \DateTime();
        $date->setTimestamp($content/1000);
        $jour = \DateTime::createFromFormat('d/m/Y H:i:s',$definirDate->aujourdhui()->format('d/m/Y').' 00:00:00');
        $dateinter =  \DateTime::createFromFormat('d/m/Y H:i:s',$date->format('d/m/Y').' 00:00:00');

        $delai = $dateinter->diff($jour);
        $nombre = $concurrence->concurrenceTotale($inter,$date);
        $nomvreRapide = $concurrence->concurrenceOtdRapideSup($inter,$date);
        if ($delai->format('%d')<6){
            $reponse->setData([
                'type'=>'rapide',
                'nombre'=>$nomvreRapide
            ]);
            }
        else{
            $reponse->setData([
                'type'=>'normale',
                'nombre'=>$nombre
            ]);

        }
        return $reponse;
    }

    /**
     *
     * @param EventDispatcherInterface $dispatcher
     * @return JsonResponse
     * @Route("/validerRdvInter/{id}")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     * @throws \JsonException
     */
    public function validerRdv($id,EventDispatcherInterface $dispatcher,Mail $mail,DefinirDate $definirDate):JsonResponse{
        $inter = $this->interventionRepository->findOneBy(['id'=>$id]);
        $content = json_decode($this->request->getContent(),false);

        if ($content){
            $debut = \DateTime::createFromFormat('Y-m-d',$content->debut);
            if ($content->fin){
                $fin = \DateTime::createFromFormat('Y-m-d',$content->fin);
                $inter->setDateDebut($debut)
                    ->setDateFin($fin);
            }
            else{
                $inter->setDateWitch($debut);
            }

        }else{
            $inter->setDateDebut(null);
        }

        $this->manager->persist($inter);
         $this->manager->flush();

        $inter->setType('dd');
        $this->manager->persist($inter);

        $event = new GenericEvent($inter);
        $dispatcher->dispatch($event,ApiMatchSalarieEvent::MATCH);

        $this->manager->flush();
        $mail->mailConfirmationInter($this->getUser()->getEmail());
        return new JsonResponse();
    }

    /**
     * @Route("/intervention/devis-{intervention}-{id}", name="devis_inter")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     *
     * @param [type] $id
     * @param [type] $intervention
     * @return Response
     */
    public function devisInter($id, $intervention): Response
    {

        $intervention = $this->interventionRepository->findOneBy(['id' => $intervention]);

        $intervention->setStatuInter('En attente de paiement');

        $this->manager->persist($intervention);

        $this->manager->flush();

        return $this->render('demandeur/encours.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/intervention/facture/{id}", name="facture_inter")
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     *
     * @param integer $id
     * @param DefinirDate $definirDate
     * @return Response
     * @throws Exception
     */
    public function factureInter($id, DefinirDate $definirDate): Response
    {
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);

        $intervention->setStatuInter('Intervention validée')
            ->setFactureAt($definirDate->aujourdhui());
        $rapport = new Rapport();
        $rapport->setIntervention($intervention);
        $intervention->setIntRap($rapport);
        $this->manager->persist($rapport);
        $this->manager->persist($intervention);

        $this->manager->flush();
        return $this->render('demandeur/mesdiags.html.twig', ['user' => $this->getUser()]);
    }

    /**
     * @param string|null $code
     * @return JsonResponse
     * @Route("/recupereAdresseInter/{code}")
     */
    public function recupererAdresse(string $code=null){
        $user = $this->getUser();
        if ($user->hasRole('ROLE_DEMANDEUR')){
            $adresse = $user->getDemandeur()->getAdresse();
        }
        elseif ($user->hasRole('ROLE_SYNDIC')){
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
            $adresse = $agent->getDemandeur()->getAdresse();
        }
        else{
            $adresse = $this->getUser()->getAgent()->getDemandeur()->getAdresse();
        }
        $reponse = new JsonResponse();
        return  $reponse->setData($adresse);
    }

    /**
     * @Security("is_granted('ROLE_RESPONSABLE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_RESPONSABLEGC')")
     * @Route("/recupInfoEtape4/{id}")
     * @param int $id
     * @return JsonResponse
     */
    public function recupInfoEtape4(int $id):JsonResponse{
        $intervention = $this->interventionRepository->find($id);
        $listeInfo = [];
        if ($intervention->getBudgetInter()){
            $listeInfo["budget"]=$intervention->getBudgetInter()->getMontant();
        }
        else{
            $listeInfo["budget"]=null;
        }
        $listeInfo["photo"]=$intervention->getNbrePhoto();
        $listeInfo["video"]=$intervention->getNbreVideo();
        if (!empty($intervention->getIntemperie())){
            $listeInfo['intemperie']=$intervention->getIntemperie();
        }
        if (!empty($intervention->getTravauxes())){
            foreach ($intervention->getTravauxes() as $travaux){
                $listeInfo['travaux'][]=$travaux->getId();
            }
        }

        return new JsonResponse($listeInfo);
    }
}
