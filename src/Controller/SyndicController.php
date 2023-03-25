<?php


namespace App\Controller;


use App\Entity\Agent;
use App\Entity\DemandeAcces;
use App\Event\UserEvent;
use App\Form\AjoutAgentType;
use App\Helper\AgentRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\DemandeAccesRepository;
use App\Service\DefinirDate;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SyndicController extends AbstractController
{
    use EntityManagerTrait,RequestTrait,AgentRepoTrait;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     * @Route ("/inscription syndic de co-propriété")
     *
     */
    public function ajoutSyndic(EventDispatcherInterface $eventDispatcher):Response{
        $agent  =new Agent();
        $form = $this->createForm(AjoutAgentType::class,$agent);
        $form->handleRequest($this->request);
        if (isset($form) && $form->isSubmitted() && $form->isValid()) {
            $roles = ['ROLE_SYNDIC','ROLE_RESPONSABLE','ROLE_ABONNE','ROLE_GRANDCOMPTE'];
            $agent->getUser()->setRoles($roles);
            $agent->setCgv($form['cgv']->getData()[0]);
            $eventAgent = new GenericEvent($agent->getUser());
            $eventDispatcher->dispatch($eventAgent,UserEvent::NAME);

            $this->manager->persist($agent);
            $this->manager->flush();
            return $this->redirectToRoute('activation');        }

        return $this->render('syndic/creerSyndiv.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @return Response
     * @Route ("/homeSyndic",name="homeSyndic")
     * @Security ("is_granted('ROLE_SYNDIC')")
     */
    public function homeSyndic():Response{
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);

        return $this->render('syndic/homeSyndic.html.twig',[
            'agent'=>$agent
        ]);
    }

    /**
     * @return Response
     * @Route("/syndic/demande accès",name="demandeAccès")
     * @Security ("is_granted('ROLE_SYNDIC')")
     */
    public function demandeAccesInterface():Response{
        return $this->render('syndic/demandeAcces.html.twig');
    }

    /**
     * @return JsonResponse
     * @Route ("/syndic/listeImmeuble")
     * @Security ("is_granted('ROLE_SYNDIC')")
     */
    public function listeImmeuble():JsonResponse{
        $ville = $this->request->getContent();
        $agents = $this->agentRepository->findForSyndic($ville);
        $liste=[];
        foreach ($agents as $agent){
            $liste[] = [
                'nom' => $agent->getDemandeur()->getProfil() . ' ' . $agent->getDemandeur()->getNom(),
                'adresse' => $agent->getDemandeur()->getAdresse()->getNumero() . ' '.$agent->getDemandeur()->getAdresse()->getNomVoie().' '.$agent->getDemandeur()->getAdresse()->getCodePostal().' '.$agent->getDemandeur()->getAdresse()->getVille(),
                'identifiant'=>$agent->getIdentifiant()
            ];
        }

        return new JsonResponse($liste);

    }

    /**
     * @param string $indentifiant
     * @return RedirectResponse
     * @Route("/syndic/demande access/{indentifiant}")
     * @Security ("is_granted('ROLE_SYNDIC')")
     */
    public function envoieDemandeAcces(string $indentifiant,DefinirDate $definirDate):RedirectResponse{
        $agent = $this->agentRepository->findOneBy(['identifiant'=>$indentifiant]);
        $demande = new DemandeAcces();
        $syndic = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $demande->setSyndicat($agent)
            ->setSyndic($syndic)
            ->setStatut('Accès demandé')
        ->setDate($definirDate->aujourdhui());
        $agent->getDemandeur()->setAcces('demandé');
        $this->manager->persist($demande);
        $this->manager->persist($agent);
        $this->manager->flush();

        return $this->redirectToRoute('mesDemandesAcces');
    }

    /**
     * @param DemandeAccesRepository $demandeAccesRepository
     * @param string|null $statut
     * @return Response
     * @Route("/syndic/mes demandes d'acces/{statut}",name="mesDemandesAcces")
     * @Security ("is_granted('ROLE_SYNDIC')")
     */
    public function mesDemandesAccess(DemandeAccesRepository $demandeAccesRepository,string $statut = null):Response{
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        if (!$statut){
            $demandes = $demandeAccesRepository->findBy(['syndic'=>$agent]);
        }
        else{
            $demandes = $demandeAccesRepository->findBy(['syndic'=>$agent,'statut'=>$statut]);
        }


        return $this->render('syndic/mesDemandeAcces.html.twig',[
            'demandes'=>$demandes
        ]);
    }
}