<?php

namespace App\Controller;



use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\DefinirDate;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    use EntrepriseRepoTrait,DemandeurRepoTrait,SalarieRepoTrait,AgentRepoTrait,RequestTrait,EntityManagerTrait;
    /**
     * @Route("/chat", name="chat")
     */
    public function index(): Response
    {
        $user = $this->getUser();

        $salarie = $this->salarieRepository->findOneBy(['user'=>$user]);

        $agent = $this->agentRepository->findOneBy(['user'=>$user]);
        if ($salarie){
            $template = 'entreprise/baseAe.html.twig';

        }
        elseif ($agent){
            $template = 'institution/baseInsti.html.twig';
        }
        else{
           $template = 'demandeur/basedemandeur.html.twig';

        }
        return $this->render('chat/index.html.twig', [
                'template'=>$template
        ]);
    }

    /**
     * @return Response
     * @Route("/chat/fenetreChat")
     */
    public function fenetreChat(){
        return $this->render('chat/fenetre.html.twig');
    }

    /**
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws Exception
     * @Route("/chat/envoieMessage")
     */
    public function envoieQestion(DefinirDate $definirDate):JsonResponse{
        $user = $this->getUser();
        $message  = new MessageChat();
        $contenu = $this->request->getContent();
        $message->setTexte($contenu)
                ->setUser($user)
            ->setDate($definirDate->aujourdhuiImmutable());
        $this->manager->persist($message);
        $this->manager->flush();
        return new JsonResponse();
    }
}
