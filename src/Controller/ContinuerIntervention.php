<?php

namespace App\Controller;

use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContinuerIntervention extends AbstractController
{
    use InterRepoTrait,EntityManagerTrait;

    /**
     * @return Response
     * @Route("/demandeur/listeInterNonTerminée/{code}",name="listeInterNonTerminée")
     * @Security ("is_granted('ROLE_DEMANDEUR')")
     */
    public function listeInterNonTerminée($code = null){
        $demandeur = $this->getUser()->getDemandeur();
        $intervention = $this->interventionRepository->findBy(['intDem'=>$demandeur,'rdvAT'=>null]);

        return $this->render('demandeur/listeInterNonTermine.html.twig',[
            'interventions'=>$intervention
        ]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @Route ("/demandeur/supprimerIntervention/{id}")
     * @Security ("is_granted('ROLE_DEMANDEUR')")
     */
    public function supprimerInter(int $id){
        $intervention = $this->interventionRepository->findOneBy(['id'=>$id]);
        $this->manager->remove($intervention);
        $this->manager->flush();

        return $this->redirectToRoute('listeInterNonTerminée');
    }
}