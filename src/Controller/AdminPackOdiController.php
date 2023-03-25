<?php

namespace App\Controller;

use App\Entity\Pack;
use App\Repository\FamilleDiagRepository;
use App\Repository\MissionRepository;
use App\Repository\PackOdiRepository;
use App\Repository\PackRepository;
use App\Repository\TypeBienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPackOdiController extends AbstractController
{
    private EntityManagerInterface $manager;
    private RequestStack $requestStack;
    private FamilleDiagRepository $familleDiagRepository;
    private PackOdiRepository $packOdiRepository;

    /**
     * @param EntityManagerInterface $manager
     * @param RequestStack $requestStack
     * @param FamilleDiagRepository $familleDiagRepository
     * @param PackOdiRepository $packOdiRepository
     * @param PackRepository $packRepository
     * @param TypeBienRepository $typeBienRepository
     */
    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack,
                                FamilleDiagRepository $familleDiagRepository, PackOdiRepository $packOdiRepository,
                                private PackRepository $packRepository,private TypeBienRepository $typeBienRepository,private MissionRepository $missionRepository)
    {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->familleDiagRepository = $familleDiagRepository;
        $this->packOdiRepository = $packOdiRepository;
        $this->packRepository = $packRepository;
        $this->typeBienRepository=$typeBienRepository;
        $this->missionRepository = $missionRepository;
    }

    /**
     * @return Response
     * @Route("/administrateur/createPack",name="createPack")
     */
    public function createPack():Response{
        $familles = $this->familleDiagRepository->findAll();
        $packs = $this->packRepository->findBy(['type'=>"Diagdrone"]);
        $types = $this->typeBienRepository->findAll();
        return $this->render("administrateur/createPack.htlm.twig",[
            'packs'=>$packs,
            'familles'=>$familles,
            'types'=>$types
        ]);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     * @Route("/administrateur/savePack")
     */
    public function savePack(): JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $pack = new Pack();
        $pack->setNom($content->nomPack)
            ->setType("Diagdrone");
        foreach ($content->mission as $id){
            $mission = $this->missionRepository->find($id);
            $pack->addMission($mission);
        }
        foreach ($content->bien as $id){
            $bien = $this->typeBienRepository->find($id);
            foreach ($bien->getTaille() as $taille){
                $pack->addTailleBien($taille);
            }
        }
        $this->manager->persist($pack);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/administrateur/modiferPackAdmin/{id}",name="modiferPackAdmin")
     */
    public function modiferPackAdmin(int $id):Response{
        $pack = $this->packRepository->find($id);
        $familles = $this->familleDiagRepository->findAll();
        return $this->render('administrateur/modifierPack.html.twig',[
            'pack'=>$pack,
            'familles'=>$familles
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/administrateur/supprimerMission/{idPack}/{idMission}",name="supprimerMission")
     */
    public function supprimerMission(int $idPack,int $idMission){
        $pack = $this->packRepository->find($idPack);
        $mission = $this->missionRepository->find($idMission);
        $pack->removeMission($mission);
        $this->manager->persist($pack);
        $this->manager->flush();
        return $this->redirectToRoute('modiferPackAdmin',['id'=>$idPack]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/administrateur/addMissionAdmin/{id}")
     */
    public function addMissionAdmin(int $id):JsonResponse{
        $content = $this->requestStack->getCurrentRequest()->getContent();
        $pack  =$this->packRepository->find($id);
        $mission = $this->missionRepository->find($content);
        $pack->addMission($mission);
        $this->manager->persist($pack);
        $this->manager->flush();
        return new JsonResponse();
    }
}