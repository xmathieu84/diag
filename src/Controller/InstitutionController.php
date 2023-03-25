<?php

namespace App\Controller;





use App\Entity\Agent;
use App\Entity\Civilite;
use App\Entity\FactureInsti;
use App\Entity\User;
use App\Event\UserEvent;
use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\PaginatorTrait;
use App\Helper\RequestTrait;
use App\Repository\AgentRepository;
use App\Repository\ComuComMailingRepository;
use App\Repository\FactureInstiRepository;
use App\Service\AgentInsti;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Fichier;
use App\Service\InterRegionDep;
use DateTime;
use Exception;
use Imagick;
use Mpdf\Mpdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class InstitutionController extends AbstractController
{
    use EntityManagerTrait,RequestTrait,PaginatorTrait,DemandeurRepoTrait,AgentRepoTrait,AboTotalInstiRepoTrait;

    /**
     * @param AgentInsti $agentInsti
     *
     * @param InterRegionDep $interRegionDep
     * @param string|null $code
     * @param DefinirDate $definirDate
     * @param FactureInstiRepository $repository
     * @return Response
     * @throws Exception
     * @Route("/home/{code}",name="homeInsti")
     * @Security("is_granted('ROLE_NIVEAU3') or is_granted('ROLE_BTP')")
     */
    public function homeInstitution(
                                    AgentInsti             $agentInsti,
                                    InterRegionDep         $interRegionDep,
                                    DefinirDate            $definirDate,
                                    FactureInstiRepository $repository,
                                    string                 $code = null): Response
    {
        $agent = $this->agentRepository->findOneBy(['user' => $this->getUser()]);

        if (!$code){
            $institution = $agent;
        }
        else{
            $institution = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        if ($institution->getCgv()===false){
            return $this->redirectToRoute('terminerInscription');
        }
        $maxAgent = $agentInsti->nombreAgent($institution->getDemandeur());
        $nombreAgent =0;
        $interventions = $interRegionDep->interventionProche($institution->getDemandeur());

        foreach ($institution->getDemandeur()->getAgents() as $agent){
            $nombreAgent ++;
            foreach ($agent->getResponsable() as $responsable){
                $nombreAgent= $responsable->getChef()->count() +$nombreAgent;
            }
        }




        return $this->render('institution/accueil.html.twig', [
            'institution' => $institution,
            'maxAgent' => $maxAgent,
            'nombreAgent' => $nombreAgent,
            'intervention'=>count($interventions),
            'code'=>$code
        ]);
    }

    /**
     * @param AgentInsti $agentInsti
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/liste/{code}",name="listeAgent")
     *
     * @Security("is_granted('ROLE_MANITOU') ")
     */
    public function createAgent(AgentInsti $agentInsti,choixTemplate $choixTemplate,string $code =null): Response
    {
        $institution = $this->agentRepository->findOneBy(['user' => $this->getUser()]);
        $agents = $institution->getChef();
        $responsables = $institution->getResponsable();
        $maxAgent = $agentInsti->nombreAgent($institution->getDemandeur());
        $nbreActuel = $agentInsti->agantActuel($institution);



        return $this->render('institution/ajoutAgent.html.twig', [
            'responsables' => $responsables,
            'agents' => $agents,
            'institution' => $institution,
            'code'=>$code,
            'maxAgent' => $maxAgent,
            'nbreActuel'=>$nbreActuel
        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/listSuperieur")
     * @Security("is_granted('ROLE_MANITOU')")
     */
    public function listeSuperieur():JsonResponse{
        $demandeur = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $superieur = [];
        $superieur[] = ['id' => $demandeur->getId(), 'nom' => $demandeur->getCivilite()->getNom() . ' ' . $demandeur->getCivilite()->getPrenom()];

        if (!$demandeur->getResponsable()->isEmpty()){
            foreach ($demandeur->getResponsable() as $responsable){

                $superieur[] = ['id' => $responsable->getId(), 'nom' => $responsable->getCivilite()->getNom() . ' ' . $responsable->getCivilite()->getPrenom()];
            }
        }
        $response = new JsonResponse();
        return $response->setData($superieur);

    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @return JsonResponse
     * @Route("/ajout personnel")
     * @Security("is_granted('ROLE_MANITOU') or is_granted('ROLE_MANITOUGC')")
     */
    public function addAgent(EventDispatcherInterface $eventDispatcher):JsonResponse{
        $content = json_decode($this->request->getContent(),true);

        $agent = new Agent();

        $role = [];
        $role[] = $content['roleG'];
        $role[]= "ROLE_ABONNE";
        $role[] = "ROLE_".$content['role'];

        $civilite = new Civilite();
        $civilite->setType($content['civilite'])
            ->setNom($content['agentNom'])
            ->setPrenom($content['agentPrenom']);
        $user = new User();
        $user->setEmail($content['email'])
            ->setPassword($content['password'])
            ->setRoles($role);

        $eventAgent = new GenericEvent($user);
        $eventDispatcher->dispatch($eventAgent,UserEvent::NAME);
        if ($content['role']==='RESPONSABLE'){
            $superieur = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
            $agent->addSuperieur($superieur);
        }
        else{
            $superieur = $this->agentRepository->findOneBy(['id'=> (int)$content['supHiera']]);
            $agent->setEmploye($superieur);
        }
        $agent->setDemandeur($superieur->getDemandeur())
        ->setCivilite($civilite)
        ->setUser($user)
        ->setCgv(true);
        $this->manager->persist($agent);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @return Response
     * @Route("/planning/{code}",name="planning")
     * @Security ("is_granted('ROLE_NIVEAU3') and is_granted('ROLE_ABONNE')")
     */
    public function planning(choixTemplate $choixTemplate,string $code=null){
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);

        return $this->render('institution/planning.html.twig',[
            'responsables'=>$agent->getResponsable(),
            'employes'=>$agent->getChef(),
            'code'=>$code

        ]);
    }


    /**
     * @param $id
     * @param AgentRepository $agentRepository
     * @return Response
     * @Route ("/créer planning/{id}/{code}",name="creationPlanning")
     * @Security("is_granted('ROLE_RESPONSABLE')")
     */
    public function creerPlanningAgent($id,AgentRepository $agentRepository,string $code=null){

        $agent = $agentRepository->findOneBy(['id'=>$id]);

        return $this->render('institution/creerPlanning.html.twig',[
            'agent'=>$agent,
            'code'=>$code
        ]);
    }

    /**
     * @Route ("/agenda/{code}",name="agenda")
     * @return Response
     * @Security ("is_granted('ROLE_NIVEAU1') and is_granted('ROLE_ABONNE') or is_granted('ROLE_NIVEAU1GC') and is_granted('ROLE_ABONNE')")
     */
    public function planningAllAgents(choixTemplate $choixTemplate,string $code=null):Response{
        $template = $choixTemplate->templateDem($this->getUser());
        return $this->render('institution/agenda.html.twig',[
            'code'=>$code
        ]);
    }

    /**
     * @param ComuComMailingRepository $repository
     * @return JsonResponse
     * @Route("/trouverCom")
     */
    public function trouverCom(ComuComMailingRepository $repository){
        $nom = $this->request->getContent();
        $coms = $repository->findForInscription($nom);
        $liste =[];
        foreach ($coms as $com){
            $liste[]=$com->getNom();
        }

        return new JsonResponse($liste);

    }
}
