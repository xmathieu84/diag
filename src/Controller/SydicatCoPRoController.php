<?php


namespace App\Controller;


use App\Helper\AgentRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Repository\DemandeAccesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SydicatCoPRoController extends AbstractController
{
    use AgentRepoTrait,EntityManagerTrait;

    /**
     * @param DemandeAccesRepository $repository
     * @return Response
     * @Route ("/mes demandes d'accès en cours/{code}",name="mesDemAccesCours")
     * @Security ("is_granted('ROLE_SYNDICAT')")
     */
        public function DemandeAccès(DemandeAccesRepository $repository,$code = null):Response{
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
            $demande = $repository->findBy(['statut'=>"Accès demandé","syndicat"=>$agent]);

            return $this->render('institution/mesDemandeAcces.html.twig',[
                'demandes'=>$demande,
                'code'=>$code
            ]);

        }

    /**
     * @param string $type
     * @param DemandeAccesRepository $repository
     * @param int $id
     * @return RedirectResponse
     * @Route("/resultatDemande/{type}-{id}",name="resultatDemande")
     * @Security ("is_granted('ROLE_SYNDICAT')")
     */
        public function resultatDemande(string $type,DemandeAccesRepository $repository,int $id):RedirectResponse{
            $demande = $repository->findOneBy(['id'=>$id]);
            if ($type === 'accepter'){
                $syndic = $demande->getSyndic();
                $agent = $demande->getSyndicat();
                $demande->setStatut('Acces accordé');
                $syndic->addSuperieur($agent);
                $this->manager->persist($demande);
                $this->manager->persist($syndic);
            }
            else{
                $demande->setStatut('Accès refusé');
                $this->manager->persist($demande);
            }
            $this->manager->flush();
            return $this->redirectToRoute('homeInsti');
        }

    /**
     * @return Response
     * @Route("/revoquer un accès/{code}",name="revoquerAccès")
     */
        public function revoquerAccès(string $code = null,DemandeAccesRepository $repository){
            $agent  = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
            $syndic = $this->agentRepository->findSyndic($agent);
            $demandeAccès = $repository->findOneBy(['syndic'=>$syndic,'syndicat'=>$agent]);

            return $this->render('institution/revoquerAccès.html.twig',[
                'code'=>$code,
                'agent'=>$agent,
                'syndic'=>$syndic,
                'demande'=>$demandeAccès
            ]);
        }

    /**
     * @param string $code
     * @return RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/annuelerAcces/{code}",name="annulerAcces")
     */
        public function annuleAcces(string $code=null):RedirectResponse{
            $agent  = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
            $syndic = $this->agentRepository->findSyndic($agent);
            $agent->removeResponsable($syndic);
            $syndic->removeSuperieur($agent);
            $this->manager->persist($agent);
            $this->manager->flush();
            return $this->redirectToRoute('revoquerAccès',['code'=>$code]);

}
}