<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Repository\FamilleDiagRepository;
use App\Repository\MissionRepository;
use App\Repository\TailleBienRepository;
use App\Repository\TypeBienRepository;
use App\Repository\TypeDiagRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissionOdiAdminCOontroller extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager,private RequestStack $requestStack,
                                private FamilleDiagRepository $familleDiagRepository,private MissionRepository $missionRepository,
                                private TypeBienRepository $typeBienRepository,private TypeDiagRepository $typeDiagRepository,private TailleBienRepository $tailleBienRepository)
    {
        $this->manager=$manager;
        $this->requestStack = $requestStack;
        $this->familleDiagRepository=$familleDiagRepository;
        $this->missionRepository=$missionRepository;
        $this->typeBienRepository = $typeBienRepository;
        $this->typeDiagRepository= $typeDiagRepository;
        $this->tailleBienRepository = $tailleBienRepository;
    }

    /**
     * @return Response
     * @Route("/administrateur/voirMission",name="voirMission")
     */
    public function voirMission():Response{
        $familles = $this->familleDiagRepository->findAll();
        $type = $this->typeBienRepository->findAll();
        return $this->render("administrateur/voirMission.html.twig",[
            'familles'=>$familles,
            'types'=>$type
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/administrateur/modifierMission/{id}")
     */
    public function modifierMission(int $id):JsonResponse{
        $nom = $this->requestStack->getCurrentRequest()->getContent();
        $mission = $this->missionRepository->find($id);
        $mission->setNom($nom);
        $this->manager->persist($mission);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     *
     * @return JsonResponse
     * @Route("/administrateur/validerMission")
     * @throws JsonException
     */
    public function validerMissionAdmin():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $type = $this->typeDiagRepository->find($content->type);
        $mission = new Mission();
        $mission->setNom($content->mission)
                ->setTypeDiag($type);
        foreach ($content->taille as $taille){
            $bien = $this->tailleBienRepository->find($taille);
            $mission->addTailleBien($bien);
        }
        $this->manager->persist($mission);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/activerMission")
     * @throws JsonException
     */
    public function activerMission():JsonResponse{
        $content  = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $mission = $this->missionRepository->find($content->id);
        $mission->setActif($content->act);
        $this->manager->persist($mission);
        dump($mission->getActif());
        $this->manager->flush();
        return new JsonResponse($content->act);
    }
}