<?php

namespace App\Controller;

use App\Helper\ListeInterTypeInterRepoTrait;
use App\Repository\InterventionRepository;
use App\Repository\ListeInterTypeInterRepository;
use DateTime;
use DateInterval;
use App\Entity\ListeInter;
use App\Entity\ListeInterTypeInter;
use App\Entity\TypInter;
use App\Form\ListeInterType;
use App\Form\TypeInterType;
use App\Helper\RequestTrait;

use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\ListeInterRepoTrait;
use App\Service\DefinirDate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminInterventionController extends AbstractController
{
    use EntityManagerTrait, RequestTrait, ListeInterRepoTrait, InterRepoTrait,ListeInterTypeInterRepoTrait;
    /**
     * @Route("/administrateur/intervention", name="adminIntervention")
     */
    public function intervention()
    {

        $listes = $this->listeInterTypeInterRepository->findForAdmin();
        $type = $this->listeInterRepository->findAll();
        return $this->render('administrateur/intervention.html.twig', [
            'type'=>$type,
            'listes' => $listes

        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/ajoutMission")
     */
    public function ajoutMission():JsonResponse{
        $content = json_decode($this->request->getContent());
        $liste = $this->listeInterRepository->findOneBy(['id'=>$content->type]);
        $type = new TypInter();
        $type->setNom($content->mission);
        $liti = new ListeInterTypeInter();
        $liti->setActif(false)
            ->setDescription($content->description)
            ->setListeInter($liste)
            ->setTypeInter($type);
        $this->manager->persist($liti);
        $this->manager->persist($type);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @Route("/administrateur/suspendreMission/{id}")
     */
    public function suspendreMission(int $id):void{
        $content = $this->request->getContent();
        $liti = $this->listeInterTypeInterRepository->findOneBy(['id'=>$id]);
        $liti->setActif($content);
        $this->manager->persist($liti);
        $this->manager->flush();
        exit();

    }


    /**
     * @Route("/administrateur/interDepasse",name="interDepasse")
     *
     * @param DefinirDate $definirDate
     * @return Response
     * @throws \Exception
     */
    public function interDateDepasse(DefinirDate $definirDate)
    {
        $date = $definirDate->aujourdhui();
        $interventions = $this->interventionRepository->interDepasse($date);

        return $this->render('administrateur/interDepasse.html.twig', ['interventions' => $interventions]);
    }

    /**
     * @Route("/administrateur/supprimerInterDepasse-{id}",name="supprimerInterDepasse")
     *
     * @param [type] $id
     * @return void
     */
    public function supprimerInterDepasse($id)
    {
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        if (!$intervention->getPropositions()->isEmpty()) {
            foreach ($intervention->getPropositions() as $proposition) {
                $this->manager->remove($proposition);
            }
        }
        $this->manager->remove($intervention);
        $this->manager->flush();

        return $this->redirectToRoute('interDepasse');
    }

    /**
     * @param ListeInterTypeInterRepository $litirepo
     * @param InterventionRepository $interventionRepository
     * @return Response
     * @Route("/administrateur/moyennePrixInter",name="moyennePrixInter")
     */
    public function moyennePrixInter(ListeInterTypeInterRepository $litirepo,InterventionRepository $interventionRepository){
        $litis = $litirepo->findAll();
        $liste =[];

        foreach ($litis as $liti){
            $sommeInter = 0;
            $interventions = $interventionRepository->findBy(['typeInter'=>$liti->getTypeInter(),'listeInter'=>$liti->getListeInter()]);
            foreach ($interventions as $intervention){
                $sommeInter +=$intervention->getPrix();
            }
            array_push($liste,['type'=>$liti->getTypeInter(),
                'listeInter'=>$liti->getListeInter(),'prix'=>$sommeInter,'inter'=>sizeof($interventions)]);

        }

        return $this->render('administrateur/moyenne.html.twig',[
            'listes'=>$liste
        ]);

    }


}
