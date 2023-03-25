<?php

namespace App\Controller;

use App\Entity\CGUvente;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\CGUventeRepository;
use App\Repository\DemandeurRepository;
use App\Service\DefinirDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

/**
 * Class CguController
 * @package App\Controller
 */
class CguController extends AbstractController
{
    use EntityManagerTrait, RequestTrait, InterRepoTrait;

    /**
     * @Route("/cgu", name="cgu")
     * @isGranted("ROLE_DEMANDEUR")
     * @param DefinirDate $definirDate
     * @return JsonResponse
     */
    public function cguOtdDemandeur(DefinirDate $definirDate,DemandeurRepository $demandeurRepository):JsonResponse
    {
        $demandeur = $demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $idInter = $this->request->getContent();
        $intervention = $this->interventionRepository->findOneBy(['id' => $idInter]);
        $date = $definirDate->aujourdhui();
        $cgu = new CGUvente();
        $cgu->setDemnadeur($demandeur)
            ->setInter($intervention)
            ->setDate($date)
            ->setCgu(true);
        $this->manager->persist($cgu);
        $this->manager->flush();
        $reponse = new JsonResponse();

        return $reponse->setData('ok');
    }

    /**
     * @Route("/refus")
     * @isGranted("ROLE_DEMANDEUR")
     * @param CGUventeRepository $cGUventeRepository
     * @return JsonResponse
     */
    public function refusCgv(CGUventeRepository $cGUventeRepository,DemandeurRepository $demandeurRepository):JsonResponse
    {
        $demandeur = $demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $idInter = $this->request->getContent();
        $intervention = $this->interventionRepository->findOneBy(['id' => $idInter]);
        $cgu = $cGUventeRepository->findOneBy(['inter' => $intervention, 'demnadeur' => $demandeur]);
        $this->manager->remove($cgu);
        $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse->setData('ok');
    }

    /**
     * @Route("/chercherCgu")
     * @isGranted("ROLE_DEMANDEUR")
     * @param CGUventeRepository $cGUventeRepository
     * @return JsonResponse
     */
    public function chercherCgu(CGUventeRepository $cGUventeRepository,DemandeurRepository $demandeurRepository):JsonResponse
    {
        $demandeur = $demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $idInter = $this->request->getContent();
        $intervention = $this->interventionRepository->findOneBy(['id' => $idInter]);
        $cgu = $cGUventeRepository->findOneBy(['inter' => $intervention, 'demnadeur' => $demandeur]);
        $reponse = new JsonResponse();
        if ($cgu === null) {
            $reponse->setData('non trouve');
        } else {
            $reponse->setData('existe');
        }
        return $reponse;
    }
}
