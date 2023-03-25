<?php

namespace App\Controller;

use App\Repository\MissionRepository;
use App\Repository\PrixOdiMissionRepository;
use App\Repository\SalarieRepository;
use App\Repository\TypeBienRepository;
use App\Service\SalarieClassIndent;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ODI')")
 */
class DureeMission extends AbstractController
{
    private EntityManagerInterface $manager;
    private RequestStack $requestStack;
    private PrixOdiMissionRepository $prixOdiMissionRepository;

    /**
     * @param EntityManager $manager
     * @param RequestStack $requestStack
     * @param PrixOdiMissionRepository $prixOdiMissionRepository
     */
    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack,
                                private MissionRepository $missionRepository,
                                PrixOdiMissionRepository $prixOdiMissionRepository,
                                private SalarieRepository $salarieRepository,private SalarieClassIndent $salarieClassIndent)
    {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->prixOdiMissionRepository = $prixOdiMissionRepository;
        $this->missionRepository =$missionRepository;
        $this->salarieClassIndent=$salarieClassIndent;
    }

    /**
     * @param TypeBienRepository $typeBienRepository
     * @param null $id
     * @return Response
     * @Route("/entreprise/tempsMission/{id}",name="tempsMission")
     */
    public function index(TypeBienRepository $typeBienRepository,$id=null):Response{
        $salarie = $this->salarieClassIndent->getSalarie($id);
        $missions = $this->missionRepository->findBySalarie($salarie);
        $type = $typeBienRepository->findAll();

        return $this->render('entreprise/dureeMission.html.twig',[
            'types'=>$type,
            'missions'=>$missions,
            'id'=>$id
        ]);
    }

    /**
     * @param null $id
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws JsonException
     * @Route("/entreprise/validerTemps/{id}")
     */
    public function validerTemps($id=null):JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $salarie = $this->salarieClassIndent->getSalarie($id);
        foreach ($content->mission as $key=>$mission){
            $mission = $this->prixOdiMissionRepository->findForTemps($mission,$content->bien[$key],$salarie);
            if ($content->temps[$key] !==""){
                $mission->setTemps($content->temps[$key]);
            }
            else{
                $mission->setTemps(0);
            }
            $this->manager->persist($mission);
        }
        $this->manager->flush();
        return new JsonResponse();
    }



    /**
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws JsonException
     * @Route("/entreprise/retrouverTempsMission/{id}")
     */
    public function retrouverTempsMission($id=null):JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $listePrix = [];
        $salarie = $this->salarieClassIndent->getSalarie($id);
        foreach ($content->mission as $key=>$mission){
            $mission = $this->prixOdiMissionRepository->findForTemps($mission,$content->bien[$key],$salarie);

            if ($mission->getTemps()){
                $listePrix[]=$mission->getTemps();
            }
            else{
                $listePrix[]=0;
            }
        }

        return new JsonResponse($listePrix);
    }
}