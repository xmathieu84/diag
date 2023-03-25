<?php

namespace App\Controller;


use App\Entity\Message;
use App\Form\ContactType;
use App\Helper\AboRepoTrait;
use App\Helper\InterRepoTrait;
use App\Helper\ListeInterRepoTrait;
use App\Helper\ListeInterTypeInterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Helper\TypeInterRepoTrait;
use App\Repository\FamilleDiagRepository;
use App\Repository\FichierOTDRepository;
use App\Repository\MissionRepository;
use App\Repository\TravauxRepository;
use App\Repository\TypeDiagRepository;
use App\Service\DefinirDate;
use App\Service\choixTemplate;
use App\Service\Geoloc;
use App\Service\Mail;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AccueilController extends AbstractController

{
    use TypeInterRepoTrait,ListeInterTypeInterRepoTrait,ListeInterRepoTrait,RequestTrait,SalarieRepoTrait;

    /**
     * @return Response
     * @Route("/", name="home")
     */
    public function index():Response
    {

        $profils = [
            [
                "profil"=>"Besoin de diagnostics immobiliers ?",
                "autre"=>"",
                'type'=>"Demandeurs",
                "interface"=>"Particulier ou professionel",
                "image"=>"",
                "sousTitre"=>"Réservez une intervention ."
            ],
            ["profil"=>
                "Diagnostiqueurs",
                'type'=>"Diagnostiqueurs",
                "autre"=>" ",
                "interface"=>"Interface",
                "image"=>"",
                "sousTitre"=>"Gérez et boostez votre activité !"
            ],



        ];




        return $this->render('accueil/index.html.twig',["profils"=>$profils]);
    }





    /**
     * @Route("/accueil-inscription", name="accueil_sub")
     * @return Response
     */
    public function accueilSub():Response
    {

        return $this->render('accueil/inscription.html.twig');
    }
    /**
     * @Route("/message",name="aprescontact")
     * @return Response
     */
    public function aprescontact():Response
    {
        return $this->render('accueil/aprescontact.html.twig');
    }

    /**
     * @Route("/contact",name = "contact")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param DefinirDate $definirDate
     * @return Response
     */
    public function contacter(EntityManagerInterface $manager, Request $request, DefinirDate $definirDate, Mail  $mail): Response
    {
        $message = new Message();
        $form = $this->createForm(ContactType::class, $message);
        $date = $definirDate->aujourdhui();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDestinataire('diag-drone');
            $message->setDate($date);
            $message->setStatut('non-lu');
            $manager->persist($message);
            $manager->flush();
            $mail->mailContact($message->getExpediteur(),$message->getSujet(),$message->getContenu());
            return $this->redirectToRoute('aprescontact');
        }
        return $this->render('accueil/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/plateforme-diag-drone.html",name="quiSomme")
     *
     * @return Response
     */
    public function quiSommesNous(): Response
    {

        return $this->render('accueil/quiSomme.html.twig', []);
    }

    /**
     * @Route("/trouver-un-telepilote-de-drone.html",name="typeintervention")
     *
     * @return Response
     */
    public function nosOffres(TypeDiagRepository $typeDiagRepository): Response
    {
        $interventions = $this->listeInterRepository->findAll();
        $familles = $typeDiagRepository->findAll();
        return $this->render('accueil/nosOffres.html.twig', [
            'intervention'=>$interventions,
            'familles'=>$familles
        ]);
    }

    /**
     * @Route("/lesProfils/{type}",name="lesProfils")
     *
     * @return Response
     */
    public function lesProfils(string $type): Response
    {
        return $this->render('accueil/nosProfils.html.twig',['type'=>$type]);
    }

    /**
     * @Route("/interfacesDediées",name="interfaceDeddies")
     *
     *
     * @return Response
     */
    public function profilEnSavoirPlus(): Response
    {
        return $this->render('accueil/interfaceDedies.html.twig', [

        ]);
    }

    /**
     * @Route("/enSavoirPlus/Abonnements/{type}",name="enSavoirPlusAbo")
     *
     * @param $type
     * @return Response
     */
    public function abonnementEnSavoirPlus($type): Response
    {
        $abonnements  = $this->abonnementsRepository->findAll();

        return $this->render('accueil/enSavoirPlusAbo.html.twig', [
            'type' => $type,
            'abonnements' => $abonnements
        ]);
    }

    /**
     * @return Response
     * @Route("/erreurMdp",name="erreurMdp")
     */
    public function errorMdp(){
        return $this->render('accueil/mpdErreur.html.twig');
    }
    /**
     * @Route("/diag-drone-2-minutes",name="dd3mn")
     *
     * @return Response
     */
    public function dd3mn(): Response
    {
        return $this->render('accueil/dd3mn.html.twig');
    }

    /**
     * @return Response
     * @Route ("/rechercheDrone",name="rechercheDrone")
     */
    public function rechercheDrone():Response{
        $typeInter = $this->listeInterRepository->findAll();
        return $this->render('accueil/rechercheDrone.html.twig',['typeInter'=>$typeInter]);
    }

    /**
     * @return JsonResponse
     * @Route("/selectTypeInter")
     */
    public function selectTypeInter():JsonResponse{
        $ListeInterTypeInters = $this->listeInterTypeInterRepository->findBy(['listeInter' => $this->request->getContent()]);
        $typeInter=[];
        foreach ($ListeInterTypeInters as $ListeInterTypeInter) {

            $typeInter[] = $ListeInterTypeInter->getTypeInter()->jsonSerialize();
        }
        $response = new JsonResponse();

        return $response->setData($typeInter);
    }

    /**
     * @param Geoloc $geoloc
     * @return JsonResponse
     * @Route("/matchOtd",name="matchOtd")
     */
    public function matchOtd(Geoloc $geoloc,FichierOTDRepository $fichierOTDRepository):JsonResponse{
        $content =json_decode($this->request->getContent(),true);
        $adresse = str_replace(' ',',',$content['adress']);

        $coordonnee = $geoloc->localise($adresse);
        $salaries = $this->salarieRepository->nombreSalarieAccueil($content['idListeInter'],$content['idTypeInter'],$coordonnee[0],$coordonnee[1]);
        if (count($salaries)<3){
            $newCoordonnee = $geoloc->distance($coordonnee[0],$coordonnee[1],75);
            $Otd = $fichierOTDRepository->findGlobal($newCoordonnee[0],$newCoordonnee[1],$newCoordonnee[2],$newCoordonnee[3]);
            $total = count($Otd)+count($salaries);
        }
        else{
            $total = count($salaries);
        }
        $response = new JsonResponse();
        return $response->setData($total);
    }

    /**
     * @return Response
     * @Route("/nosPartenaires",name="nosParts")
     */
    public function partenaire(TravauxRepository $repository){
        $travaux = $repository->findAll();
        return $this->render('accueil/nosPartenaire.html.twig',[
            'travaux'=>$travaux
        ]);
    }

    /**
     * @return Response
     * @Route ("/mentionLegales",name="mentionsLegales")
     */
    public function mentionsLegales(){
        return $this->render('accueil/mentionLegales.html.twig');
    }

    /**
     * @return Response
     * @Route("/condition générales",name="cg")
     */
    public function conditionGen(){
        return $this->render('accueil/conditionGen.html.twig');
    }

    /**
     * @return Response
     * @Route ("/espacesPublicitaire/{type}",name="espacePub")
     */
    public function espacePub($type):Response{

        return $this->render("accueil/espacePub.html.twig",[
            'type'=>$type
        ]);
    }

    /**
     * @return Response
     * @Route("/recherche/diagnostiqueurs.html",name="rechercheDiag")
     */
    public function rechercheDiag():Response{

        return $this->render('accueil/rechercheDiag.html.twig');
    }

}
