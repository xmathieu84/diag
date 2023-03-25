<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Demandeur;
use App\Event\AdressEvent;
use App\Event\UserEvent;
use App\Form\DemandeurType;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\AmbassadeurRepoTrait;
use App\Helper\CodePromoRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\MailPrefectureRepository;
use App\Service\AbonnementInstitutionnel;
use App\Service\codeActivation;
use App\Service\DefinirDate;
use App\Service\Mail;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoproprieteController extends AbstractController
{
    use EntityManagerTrait,RequestTrait,AbonnementGcirepoTrait,DemandeurRepoTrait,CodePromoRepoTrait,AmbassadeurRepoTrait;

    /**
     * @param Mail $mail
     * @param EventDispatcherInterface $eventDispatcher
     * @param DefinirDate $definirDate
     * @param AbonnementInstitutionnel $abonnementInstitutionnel
     * @param codeActivation $codeActivation
     * @param MailPrefectureRepository $repository
     * @return Response
     * @throws \Exception
     * @Route ("/syndicatCorpoprietes",name="syndicatCorpoprietes")
     */
    public function inscriptionCoPro(Mail $mail,EventDispatcherInterface $eventDispatcher,
                                     DefinirDate $definirDate,AbonnementInstitutionnel $abonnementInstitutionnel,
                                     codeActivation $codeActivation,MailPrefectureRepository $repository){

        $demandeur = new Demandeur();
        $agent = new Agent();
        $form = $this->createForm(DemandeurType::class,$demandeur);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()){
            $agent->setDemandeur($demandeur)
                ->setUser($form['user']->getData())
                ->setCivilite($demandeur->getCivilite())
                ->setCgv(false);
            $demandeur->setCivilite(null);
            $mail->mailInscriptionAgent($agent);
            $agent->getUser()->setRoles(['ROLE_GRANDCOMPTE', "ROLE_MANITOU",'ROLE_SYNDICAT']);
            if ($form['codePromo']->getData()){
                $code = $this->codePromoRepository->findOneBy(['profil'=>"Syndicat de co-propriété",'codeReduc'=>$form['codePromo']->getData()]);

            }
            else{
                $code=null;
            }
            if ($code){
                $abonnement = $abonnementInstitutionnel->abonnementInstiPromo($code,$demandeur);
            }

            else{
                $abonnement = $abonnementInstitutionnel->choixAbonnementGc($demandeur,2,"Syndicat de co-propriété");
            }
            $localisationEvent = new GenericEvent($demandeur);
            $eventDispatcher->dispatch($localisationEvent,  AdressEvent::NAME);
            $accesDemandeurEvent = new GenericEvent($form['user']->getData());
            $eventDispatcher->dispatch($accesDemandeurEvent, UserEvent::NAME);

            $this->manager->persist($demandeur);


            $this->manager->persist($agent);

            $this->manager->persist($abonnement);
            $this->manager->flush();
            return $this->redirectToRoute('activation');

        }

        return $this->render("demandeur/inscriptionCopro.html.twig",[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/abonnementCoPro")
     */
    public function abonnementCoPro():JsonResponse{
        $abonnement = $this->abonnementGciRepository->findOneBy(['profil'=>"Syndicat de co-propriété","utlisateur"=>2]);
        $response = new JsonResponse();

        return $response->setData([
            'ht'=> $abonnement->getPrix(),
            'utilisateur'=>$abonnement->getUtlisateur()." utilisateurs",
            'ttc'=>$abonnement->getPrix() *1.2
        ]);
    }
}