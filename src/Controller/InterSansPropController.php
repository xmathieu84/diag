<?php

namespace App\Controller;

use App\Entity\Proposition;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\PropositionRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Geoloc;
use App\Service\InterConcurrence;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

class InterSansPropController extends AbstractController
{
    use InterRepoTrait, RequestTrait, EntityManagerTrait, SalarieRepoTrait, InterRepoTrait,PropositionRepoTrait;
    /**
     * @Route("/aucunePropositions", name="inter_sans_prop")
     * @Security ("is_granted('ROLE_SALARIE') and (is_granted('ROLE_FREE') or is_granted('ROLE_PREMIUM'))")
     *
     */
    public function index(DefinirDate $definirDate, Geoloc $geoloc, choixTemplate $choixTemplate,InterConcurrence $concurrence)
    {
        $date = $definirDate->aujourdhui();
        $delai = $definirDate->duree($date, 'P2D');
        $template = $choixTemplate->templateSal($this->getUser());
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $coordonnees = $template[0]->getAdresse()->getCoordonnees();
        $interventions = $this->interventionRepository->interSansProp($delai,$salarie->getEntreprise()->getId());
        $interSansProp = $concurrence->nbreInterSansProp($interventions,$salarie,$coordonnees);

        return $this->render('inter_sans_prop/index.html.twig', [
            'interSansProp' => $interSansProp,
            'template' => $template[1],
            'salarie' => $template[0]
        ]);
    }

    /**
     * @Route("/validerProp/{id}",name="validerProp")
     * @isGranted("ROLE_SALARIE")
     * @param DefinirDate $definirDate
     * @param $id
     * @return RedirectResponse
     */
    public function validerProp(DefinirDate $definirDate,$id):RedirectResponse
    {
      $salarie = $this->getUser()->getSalarie();
      $date = $definirDate->duree($definirDate->aujourdhui(),'P1DT12H');
      $inter = $this->interventionRepository->findOneBy(['id'=>$id]);
      $proposition = new Proposition();
      $proposition->setSalarie($salarie)
                    ->setInter($inter)
                    ->setDateFin($date);
      $this->manager->persist($proposition);
      $this->manager->flush();
      return $this->redirectToRoute("demcours");

    }
}
