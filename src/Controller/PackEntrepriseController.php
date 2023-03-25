<?php

namespace App\Controller;

use App\Repository\FamilleDiagRepository;
use App\Repository\MissionRepository;
use App\Repository\PackRepository;
use App\Repository\SalarieRepository;
use App\Service\PackService;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PackEntrepriseController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(private MissionRepository $missionRepository,
                                private SalarieRepository $salarieRepository,
                                private PackRepository $packRepository,
                                private RequestStack $requestStack,private EntityManagerInterface $manager)
    {
        $this->missionRepository=$missionRepository;
        $this->salarieRepository = $salarieRepository;
        $this->packRepository = $packRepository;
        $this->requestStack = $this->requestStack;
        $this->manager=$manager;
    }

    /**
     *
     * @return Response
     * @Route("/entreprise/choix-cible",name="packPT")
     */
    public function packPourTous():Response{

        return $this->render("entreprise/packPourTous.html.twig",[

        ]);
    }

    /**
     * @param PackService $packService
     * @param int|null $id
     * @return Response
     * @Route("/entreprise/liste/{id}",name="listePackEnt")
     */
    public function listePackOdi(PackService $packService,int $id=null): Response
    {
        $salarie=  $this->salarieRepository->find($id);
        $missions = $this->missionRepository->findBySalarie($salarie);
        $packs = $packService->sortPack($this->packRepository->findByEntreprise($salarie->getEntreprise()),$missions);
        $packsPerso = $packService->sortPack($this->packRepository->findOtherPack($salarie->getEntreprise()),$missions);
        $packSouscrit = $this->packRepository->findBySalarie($salarie);

         return $this->render("entreprise/liste.html.twig",[
             'packs'=>$packs,
             'salarie'=>$salarie,
             'packPerso'=>$packsPerso,
             "packSouscrit"=>$packSouscrit
         ]);
    }

    /**
     * @param FamilleDiagRepository $familleDiagRepository
     * @return Response
     * @Route("/entreprise/modifierPack",name="modiferPack")
     */
    public function modifierPack(FamilleDiagRepository $familleDiagRepository):Response{
        $packs = $this->packRepository->findOtherPack($this->getUser()->getSalarie()->getEntreprise());
        $familles = $familleDiagRepository->findAll();
        return $this->render('entreprise/modifierPackPerso.html.twig',[
            'packs'=>$packs,
            'familles'=>$familles
        ]);
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/supprimerMissionPack")
     */
    public function supprimerMissionPack():JsonResponse{
        $content  = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $pack = $this->packRepository->find($content->idPack);
        $mission = $this->missionRepository->find($content->idMission);
        $pack->removeMission($mission);
        /*$this->manager->persist($pack);
        $this->manager->flush();*/
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route("/entreprise/ajouterMission")
     * @throws JsonException
     */
    public function ajouterMission():JsonResponse{

        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $pack = $this->packRepository->find($content->idPack);
        $mission = $this->missionRepository->find($content->idMission);
        $pack->addMission($mission);
        $salaries = $this->salarieRepository->findByPackEntreprise($pack,$this->getUser()->getSalarie()->getEntreprise());
        foreach ($salaries as $salarie){
            $missions = $this->missionRepository->findBySalarie($salarie);
            if (in_array($mission,$missions,true)){

            }
        }
        /*this->manager->persist($pack);
        $this->manager->flush();*/
        return new JsonResponse([
            "typeDiag"=>$mission->getTypeDiag()->getNom(),
            "id"=>$mission->getId(),
            "mission"=>$mission->getNom()

        ]);
    }
}