<?php

namespace App\Controller;

use App\Entity\AboTotalInsti;
use App\Entity\Agent;
use App\Entity\Demandeur;
use App\Event\AdressEvent;
use App\Event\UserEvent;
use App\Form\EntrepriseTPType;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\ProBtpRepository;
use App\Repository\TravauxRepository;
use App\Repository\UserRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Fichier;
use App\Service\Geoloc;
use App\Service\Mail;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class ProBtpController extends AbstractController
{
    use RequestTrait,EntityManagerTrait,AbonnementGcirepoTrait,AgentRepoTrait,DemandeurRepoTrait;


    /**
     * @return Response
     * @Route("/inscriptionProBtp/{type}",name="inscriptionProBtp")
     */
    public function inscriptionProBtp(EventDispatcherInterface $eventDispatcher,Geoloc $geoloc,Fichier $fichier,DefinirDate $definirDate,Mail $mail,$type):Response{

        $demandeur = new Demandeur();
        $demandeur->setProfil("Entreprise");
        $form = $this->createForm(EntrepriseTPType::class,$demandeur,['profil'=>$type]);
        $role =[];
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()){
            $agent = new Agent();
            $agent->setDemandeur($demandeur)
                    ->setUser($form['user']->getData())
                    ->setCivilite($demandeur->getCivilite())
                    ->setCgv(false);
            $demandeur->setUser(null);
            $accesDemandeurEvent = new GenericEvent($form['user']->getData());
            $eventDispatcher->dispatch($accesDemandeurEvent, UserEvent::NAME);
            if ($type ==='gc'){
                if ($form["abonnementPub"]->getData()==="oui"){

                    foreach ($form['proBtp']['travaux']->getData() as $travaux){
                        $demandeur->getProBtp()->addTravaux($travaux);
                    }
                    $role[]= 'ROLE_BTP';
                    $event = new GenericEvent($demandeur);

                    if ($form['proBtp']['departZoneInter']->getData() ==='non'){
                        $lieu = $geoloc->localise($form['proBtp']['villeDepart']->getData());
                        $distance = $geoloc->distance($lieu[1],$lieu[2],$form['proBtp']['distanceInter']->getData());
                    }else{
                        $eventDispatcher->dispatch($event,AdressEvent::NAME);
                        $distance  =$geoloc->distance($demandeur->getAdresse()->getCoordonnees()->getLatitude(),$demandeur->getAdresse()->getCoordonnees()->getLongitude(),$form['proBtp']['distanceInter']->getData());

                    }
                    $demandeur->getAdresse()->getCoordonnees()->setLatMinInter($distance[0])
                        ->setLatMaxInter($distance[1])
                        ->setLonMinInter($distance[2])
                        ->setLonMaxInter($distance[3]);


                    $aboTotalPub = new AboTotalInsti();
                    $aboTotalPub->setAbonnement($form['proBtp']['abonnement']->getData())
                        ->setDemandeur($demandeur)
                        ->setDebut($definirDate->aujourdhuiImmutable())
                        ->setAbonne(true)
                        ->setTotal($form['proBtp']['abonnement']->getData()->getPrix()*12)
                        ->setFin($definirDate->aujourdhuiImmutable()->add(new \DateInterval('P1Y')));

                    $this->manager->persist($aboTotalPub);
                    
                }
                array_push($role,"ROLE_GRANDCOMPTE");
                array_push($role,"ROLE_MANITOU");
                $aboTotal = new AboTotalInsti();
                $aboTotal->setAbonnement($form['abonnement']->getData())
                    ->setDemandeur($demandeur)
                    ->setAbonne(true)
                    ->setDebut($definirDate->aujourdhuiImmutable())
                    ->setTotal($form['abonnement']->getData()->getPrix()*1.2)
                    ->setFin($definirDate->aujourdhuiImmutable()->add(new \DateInterval('P1Y')));
            }
            if ($type ==='proBtp'){
                if ($form["abonnementPub"]->getData()==="oui"){
                    array_push($role,"ROLE_GRANDCOMPTE");
                    array_push($role,"ROLE_MANITOU");
                    $aboTotalGc = new AboTotalInsti();
                    $aboTotalGc->setAbonnement($form['abonnement']->getData())
                        ->setDemandeur($demandeur)
                        ->setAbonne(true)
                        ->setDebut($definirDate->aujourdhuiImmutable())
                        ->setTotal($form['abonnement']->getData()->getPrix()*1.2)
                        ->setFin($definirDate->aujourdhuiImmutable()->add(new \DateInterval('P1Y')));
                    $this->manager->persist($aboTotalGc);
                }

                foreach ($form['proBtp']['travaux']->getData() as $travaux){

                    $demandeur->getProBtp()->addTravaux($travaux);
                }
                $role[]= 'ROLE_BTP';
                $event = new GenericEvent($demandeur);
                $eventDispatcher->dispatch($event,AdressEvent::NAME);
                if ($form['proBtp']['departZoneInter']->getData() ==='non'){
                    $lieu = $geoloc->localise($form['proBtp']['villeDepart']->getData());

                    $distance = $geoloc->distance($lieu[0],$lieu[1],$form['proBtp']['distanceInter']->getData());
                }else{
                    $eventDispatcher->dispatch($event,AdressEvent::NAME);
                    $distance  =$geoloc->distance($demandeur->getAdresse()->getCoordonnees()->getLatitude(),$demandeur->getAdresse()->getCoordonnees()->getLongitude(),$form['proBtp']['distanceInter']->getData());

                }
                $demandeur->getAdresse()->getCoordonnees()->setLatMinInter($distance[0])
                    ->setLatMaxInter($distance[1])
                    ->setLonMinInter($distance[2])
                    ->setLonMaxInter($distance[3]);
                
                $aboTotal = new AboTotalInsti();
                $aboTotal->setAbonnement($form['proBtp']['abonnement']->getData())
                    ->setDemandeur($demandeur)
                    ->setDebut($definirDate->aujourdhuiImmutable())
                    ->setAbonne(true)
                    ->setTotal($form['proBtp']['abonnement']->getData()->getPrix()*1.2)
                    ->setFin($definirDate->aujourdhuiImmutable()->add(new \DateInterval('P1Y')));

                $this->manager->persist($aboTotal);


            }
            if ($form['logo']->getData()){
                $newLogo = $fichier->saveFile($demandeur->getNom(),$this->getParameter('logo_directory'),$form['logo']->getData());
                $demandeur->setLogo($newLogo);
            }
            $agent->getUser()->setRoles($role);

            $this->manager->persist($agent);
            $this->manager->persist($demandeur);
            $this->manager->persist($aboTotal);
             $this->manager->flush();
            $mail->mailInscriptionAgent($agent);
            return $this->redirectToRoute('activation');
        }
        return $this->render('demandeur/proBtpInscription.html.twig',[
            'form'=>$form->createView(),
            'type'=>$type
        ]);
    }

    public function nosPartenaire():Response{
         return $this->render('demandeur/NosPartenaireProBtp.html.twig');
    }

    /**
     * @param Geoloc $geoloc
     * @return JsonResponse
     * @Route ("/localisationInscriptionProBtp")
     */
    public function localisationInscriptionProBtp(Geoloc $geoloc):JsonResponse{
        $content = $this->request->getContent();
        $coordonees= $geoloc->localise($content);
        return new JsonResponse($coordonees);
    }

    /**
     * @return JsonResponse
     * @Route("/localisationPremiumProBtp")
     */
    public function localisationPremiumProBtp(TravauxRepository $travauxRepository,ProBtpRepository $proBtpRepository):JsonResponse{
        $content = json_decode($this->request->getContent());
        $liste = [];
        $resultat = [];
        foreach ($content as $id){
            $travail = $travauxRepository->findOneBy(['id'=>$id]);

            $proBtps = $proBtpRepository->findByTravaux($travail);
            foreach ($proBtps as $proBtp){
                $liste[] = $proBtp;
            }
        }
        $listeFinale = array_unique($liste,SORT_REGULAR);
       foreach ($listeFinale as $pro){
            array_push($resultat,[
                'coordonee'=>[$pro->getDemandeur()->getAdresse()->getCoordonnees()->getLatitude(),$pro->getDemandeur()->getAdresse()->getCoordonnees()->getLongitude()],
                'rayon'=>$pro->getDistanceInter()
            ]);
        }

        return new JsonResponse($resultat);
    }

    /**
     * @param ProBtpRepository $btpRepository
     * @param Geoloc $geoloc
     * @param TravauxRepository $travauxRepository
     * @return JsonResponse
     * @route ("/rechercheProBtpAccueil")
     */
    public function rechercheProBtpAccueil(ProBtpRepository $btpRepository,Geoloc $geoloc,TravauxRepository $travauxRepository):JsonResponse{
            $content =json_decode($this->request->getContent());
            $travaux = $travauxRepository->findOneBy(['id'=>$content->inter]);
            $coordonnee = $geoloc->localise($content->ville.' '.$content->cp);
            $proPremium1 = $btpRepository->findForPubcibleInter($travaux,$coordonnee[0],$coordonnee[1],'premium',4);
            $proPremium2 = $btpRepository->findForPubcibleInter($travaux,$coordonnee[0],$coordonnee[1],'premium',null);
            $autre = $btpRepository->findForPubcibleInter($travaux,$coordonnee[0],$coordonnee[1],'classique',null);
            $liste =[];

            $pros = array_merge(array_merge($proPremium1,$proPremium2) , $autre);

            foreach (array_unique($pros,SORT_REGULAR) as $pro){


                array_push($liste,[
                    'boss'=>$pro->getId(),
                    'nom'=>$pro->getDemandeur()->getNom(),
                    'logo'=>$pro->getDemandeur()->getLogo()

                ]);
            }

            if ($this->getUser() ===null){
                return new JsonResponse('non');
            }
            else{
                return new JsonResponse($liste);
            }

    }

    /**
     * @param $id
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/contacterPro/{id}",name="contacterPro")
     * @Security("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_MANITOU')")
     */
    public function contacterPro($id,choixTemplate $choixTemplate,ProBtpRepository $btpRepository):Response{
        $user = $this->getUser();
        $template = $choixTemplate->templateCg($user);
        $pro = $btpRepository->findOneBy(['id'=>$id]);
        return $this->render('demandeur/contacterPro.html.twig',[
            'template'=>$template,
            'expediteur'=>$user,
            'destinataire'=>$pro

        ]);

    }

    /**
     * @return JsonResponse
     * @Route("/envoieMailProBtp")
     */
    public function envoieMailProBtp(UserRepository $repository,ProBtpRepository $btpRepository,Mail $mail):JsonResponse{
        $content = json_decode($this->request->getContent());
        $user = $repository->findOneBy(['email'=>$content->expe]);
        $pro = $btpRepository->findOneBy(['id'=>$content->proBtp]);
        $respPpro = $this->agentRepository->findResponsable($pro);

        $demandeur = $this->demandeurRepository->findOneBy(['user'=>$user]);
        if ($demandeur !==null){
            $identite = $demandeur->getCivilite()->getNom().' '.$demandeur->getCivilite()->getPrenom();
            $telephone = $demandeur->getTelephon()->getNumero();
        }
        else{
            $agent = $this->agentRepository->findOneBy(['user'=>$user]);
            $identite = $agent->getDemandeur()->getNom().' ('.$demandeur->getCivilite()->getNom().' '.$demandeur->getCivilite()->getPrenom().')';
            $telephone = $agent->getDemandeur()->getTelephon()->getNumero();
        }
        $mail->mailToPartenairePro($content->titre,$content->message,$identite,$telephone,$user->getEmail(),$respPpro->getUser()->getEmail());
        return new JsonResponse('ok');
    }




}