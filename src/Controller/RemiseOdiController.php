<?php

namespace App\Controller;

use App\Entity\RemiseException;
use App\Entity\RemiseTemps;
use App\Repository\MissionOdiRepository;
use App\Repository\MissionRepository;
use App\Repository\PackOdiRepository;
use App\Repository\PackRepository;
use App\Repository\RemiseExceptionRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemiseOdiController extends AbstractController
{
    private RequestStack $requestStack;
    private EntityManagerInterface $manager;

    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $manager
     * @param RemiseExceptionRepository $remiseExceptionRepository
     * @param DefinirDate $definirDate
     * @param PackRepository $packRepository
     * @param MissionRepository $missionRepository
     * @param MissionOdiRepository $missionOdiRepository
     * @param PackOdiRepository $packOdiRepository
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $manager,
                                private RemiseExceptionRepository $remiseExceptionRepository,private DefinirDate $definirDate,
                                private PackRepository $packRepository,private MissionRepository $missionRepository,
                                private MissionOdiRepository $missionOdiRepository,private PackOdiRepository $packOdiRepository)
    {
        $this->requestStack = $requestStack;
        $this->manager = $manager;
        $this->remiseExceptionRepository=$remiseExceptionRepository;
        $this->definirDate = $definirDate;
        $this->packRepository= $packRepository;
        $this->missionRepository = $missionRepository;
        $this->missionOdiRepository = $missionOdiRepository;
        $this->packOdiRepository = $packOdiRepository;
    }

    /**
     * @return Response
     * @throws Exception
     * @Route("/entreprise/remise",name="remise")
     */
    public function voirRemise():Response{
        $remiseMission = $this->remiseExceptionRepository->findBySalarieMission($this->definirDate->aujourdhui());
        $remisePack = $this->remiseExceptionRepository->findBySalariePack($this->definirDate->aujourdhui());

        return $this->render('entreprise/remise.html.twig',[
            'remises'=>array_merge($remiseMission,$remisePack)
        ]);
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/validerChoix")
     */
    public function validerChoix():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        if ($this->getUser()->getSalarie()->getRemiseTemps()){
            $remise = $this->getUser()->getSalarie()->getRemiseTemps();
        }
        else{
            $remise = new RemiseTemps();
        }

        $salarie = $this->getUser()->getSalarie();
        $remise->setOdi($salarie)
            ->setDemiJournee($content->demiJournee)
            ->setJournee($content->journee);
        $this->manager->persist($remise);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param string $type
     * @return JsonResponse
     * @Route("/entreprise/recupereType/{type}")
     */
    public function recupereType(string $type):JsonResponse{
        $listeF = [];

        $missions = $this->missionRepository->findBySalarie($this->getUser()->getSalarie());
        $packs = $this->packRepository->findBySalarie($this->getUser()->getSalarie());
        switch ($type){
            case 'diag':
                foreach ($missions as $mission){
                    $listeF[]= [
                        'id'=>$mission->getId(),
                        'type'=>$mission->getNom()
                    ];
                }
                break;
            case 'packs':
                foreach ($packs as $pack){
                    $listeF[]= [
                        'id'=>$pack->getId(),
                        'type'=>$pack->getNom()
                    ];
                }
                break;
            case 'tout':
                $listeF[]=null;
        }
        return new JsonResponse($listeF);
    }

    /**
     * @param string $type
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/validerRemise/{type}")
     */
    public function validerRemise(string $type):JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        dump($content);
        if (($type ==='diag'|| $type ==="packs") && empty($content->mission)){
            return new JsonResponse("Veuillez choisir une mission ou un pack s'il vous plait");
        }

        elseif ($type){
            if ($type ==="tout"){
                $missions = $this->missionRepository->findBySalarie($this->getUser()->getSalarie());
                $packs = $this->packRepository->findBySalarie($this->getUser()->getSalarie());
                foreach ($missions as $mission){
                    $missionOdi = $this->missionOdiRepository->findOneBy(['odi'=>$this->getUser()->getSalarie(),'mission'=>$mission]);
                    $remise = new RemiseException();
                    $remise->setMission($missionOdi)
                        ->setDebut(DateTime::createFromFormat('Y-m-d', $content->debut))
                        ->setFin(DateTime::createFromFormat("Y-m-d",$content->fin))
                        ->setTaux($content->montant);
                    $this->manager->persist($remise);
                }
                foreach ($packs as $pack){
                    $packOdi = $this->packOdiRepository->findOneBy(['odi'=>$this->getUser()->getSalarie(),"pack"=>$pack]);
                    $remisePack = new RemiseException();
                    $remisePack->setPack($packOdi)
                        ->setDebut(DateTime::createFromFormat('Y-m-d', $content->debut))
                        ->setFin(DateTime::createFromFormat("Y-m-d",$content->fin))
                        ->setTaux($content->montant);
                    $this->manager->persist($remisePack);
                }
                $this->manager->flush();
                return new JsonResponse("Vos remises ont été enregistrées");
            }
            elseif ($type==='diag'){
                $missions = $this->missionRepository->findBySalarie($this->getUser()->getSalarie());
                foreach ($content->mission as $idMission){
                    $mission = $this->missionRepository->find($idMission);
                    $missionOdi = $this->missionOdiRepository->findOneBy(['odi'=>$this->getUser()->getSalarie(),'mission'=>$mission]);
                   $remise = new RemiseException();
                   $remise->setMission($missionOdi)
                        ->setDebut(DateTime::createFromFormat('Y-m-d', $content->debut))
                        ->setFin(DateTime::createFromFormat("Y-m-d",$content->fin))
                        ->setTaux($content->montant);
                    $this->manager->persist($remise);
                }
                $this->manager->flush();
                return new JsonResponse("Vos remises sur vos missions selectionées ont été enregistrées");
            }
            else{
                foreach ($content->mission as $idPack){
                    $pack = $this->packRepository->find($idPack);
                    $packOdi = $this->packOdiRepository->findOneBy(['odi'=>$this->getUser()->getSalarie(),"pack"=>$pack]);
                    $remisePack = new RemiseException();
                    $remisePack->setPack($packOdi)
                        ->setDebut(DateTime::createFromFormat('Y-m-d', $content->debut))
                        ->setFin(DateTime::createFromFormat("Y-m-d",$content->fin))
                        ->setTaux($content->montant);
                    $this->manager->persist($remisePack);
                }
                $this->manager->flush();
                return new JsonResponse("Vos remises sur les packs selectionnés ont été enregistrées");
            }
        }

    }

    /**
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/entreprise/validerMarket")
     */
    public function validerMarket():JsonResponse{
        $content = $this->requestStack->getCurrentRequest()->getContent();
        $salarie = $this->getUser()->getSalarie();
        $salarie->setMarketPlace($content);
        $this->manager->persist($salarie);
        $this->manager->flush();
        return new JsonResponse();
    }
}