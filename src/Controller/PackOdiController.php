<?php

namespace App\Controller;

use App\Entity\Pack;
use App\Entity\PackOdi;
use App\Helper\SalarieRepoTrait;
use App\Repository\FamilleDiagRepository;
use App\Repository\MissionRepository;
use App\Repository\PackOdiRepository;
use App\Repository\PackRepository;
use App\Repository\SalarieRepository;
use App\Repository\TailleBienRepository;
use App\Repository\TypeBienRepository;
use App\Service\choixTemplate;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PackOdiController extends AbstractController
{
    use SalarieRepoTrait;
    private RequestStack $requestStack;
    private PackRepository $packRepository;
    private EntityManagerInterface $manager;
    private FamilleDiagRepository $familleDiagRepository;
    private MissionRepository $missionRepository;
    private TypeBienRepository $typeBienRepository;
    private TailleBienRepository $tailleBienRepository;

    /**
     * @param RequestStack $requestStack
     * @param PackRepository $packRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(RequestStack $requestStack,TailleBienRepository $tailleBienRepository,TypeBienRepository $typeBienRepository, PackRepository $packRepository, EntityManagerInterface $manager,FamilleDiagRepository $familleDiagRepository,MissionRepository $missionRepository)
    {
        $this->requestStack = $requestStack;
        $this->packRepository = $packRepository;
        $this->manager = $manager;
        $this->familleDiagRepository = $familleDiagRepository;
        $this->missionRepository = $missionRepository;
        $this->typeBienRepository = $typeBienRepository;
        $this->tailleBienRepository=$tailleBienRepository;
    }

    /**
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/entreprise/listePack",name="listePack")
     */
    public function listePack(choixTemplate $choixTemplate):Response{
        $salarie = $this->getUser()->getSalarie();
        $templateSalarie = $choixTemplate->templateAE($salarie->getEntreprise());
        $packs = $this->packRepository->findBy(['type'=>"diagDrone"]);
        $missions = $this->missionRepository->findBySalarie($salarie);
        $listeMission = [];
       foreach ($packs as $pack){
           $reponse = true;
           foreach ($pack->getMissions() as $missionPack){
               if (!in_array($missionPack,$missions,true)){
                   $reponse = false;
                   break;
               }
           }
           if ($reponse){

               $listeMission[]=$pack;
           }
       }
        $missions = $salarie->getDetailPrixes()->toArray();
        $liste = [];
        $type = $this->typeBienRepository->findAll();
        foreach ($missions as $mission){
            $liste[$mission->getMission()->getTypeDiag()->getFamilleDiag()->getNom()][$mission->getMission()->getTypeDiag()->getNom()][] = $mission->getMission();
        }

        $packPerso = $this->packRepository->findPackPerso($salarie);
        return $this->render('entreprise/listePackOdi.html.twig',[
            'template'=>$templateSalarie,
            'packs'=>$listeMission,
            "liste"=>$liste,
            'packPersos'=>$packPerso,
            'types'=>$type,
            'salarie'=>$salarie
        ]);
    }
    /**
     * @Route("/entreprise/souscrirePack/{idOdi}")
     */
    public function souscrirePack(int $idOdi=null):JsonResponse{
        $id = $this->requestStack->getCurrentRequest()->getContent();
        if ($idOdi){
            $salarie = $this->salarieRepository->find($idOdi);
        }
        else{
            $salarie = $this->getUser()->getSalarie();
        }
        $pack = $this->packRepository->find($id);
        $packOdi = (new PackOdi())->setOdi($salarie)
            ->setPack($pack);

        $this->manager->persist($packOdi);
        $this->manager->flush();

        return new JsonResponse($packOdi->getId());
    }

    /**
     * @return JsonResponse
     * @Route("/entreprise/listePackSouscrit")
     */
    public function listePackSouscrit():JsonResponse{
        $odi = $this->getUser()->getSalarie();
        $listePack=[];
        foreach ($odi->getPackOdis('diag') as $packOdi){
            if ($packOdi->getPack()->getType()==="Diagdrone"){
                $listePack[] = $packOdi->getPack()->getId();
            }
        }

         return new JsonResponse($listePack);
    }

    /**
     * @param PackOdiRepository $packOdiRepository
     * @return JsonResponse
     * @Route("/entreprise/retirerPack/{idOdi}")
     */
    public function retirerPack(PackOdiRepository $packOdiRepository,int $idOdi = null):JsonResponse{
        $id = $this->requestStack->getCurrentRequest()->getContent();
        if (!$idOdi){
            $odi = $this->getUser()->getSalarie();
        }else{
            $odi  =$this->salarieRepository->find($idOdi);
        }

        $pack = $this->packRepository->find($id);
        $packOdi = $packOdiRepository->findOneBy(['odi'=>$odi,'pack'=>$pack]);

       foreach ($packOdi->getPackOdiPrixTailles() as $prixTaille){
            if ($prixTaille){
                $this->manager->remove($prixTaille);
            }
        }
        $this->manager->remove($packOdi);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/validerpack")
     */
    public function validerPackPerso():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $pack = new Pack();
        $pack->setNom($content->nom)
            ->setType('pack perso');
        foreach ($content->id as $id){
            $mission = $this->missionRepository->find($id);
            $pack->addMission($mission);
        }

        foreach ($content->bienExlu as $bien){
            $typeBien = $this->typeBienRepository->find($bien);

            foreach ($typeBien->getTaille() as $taille){
                $pack->addTailleBien($taille);
            }
        }

        $packOdi = new PackOdi();
        $packOdi->setPack($pack)
            ->setOdi($this->getUser()->getSalarie());
        $this->manager->persist($packOdi);
        $this->manager->persist($pack);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @Route("/entreprise/supprimerPack/{id}", name="suppimerPack")
     */
    public function supprimerPackPerso(int $id):RedirectResponse{
        $pack = $this->packRepository->find($id);
        foreach ($pack->getPackOdis() as $packOdi){
            $this->manager->remove($packOdi);
            foreach ($packOdi->getPackOdiPrixTailles() as $prixTaille){
                if ($prixTaille){
                    $this->manager->remove($prixTaille);
                }

            }
        }
        foreach ($pack->getTailleBiens() as $tailleBien){
            if ($tailleBien){
                $this->manager->remove($tailleBien);
            }
        }
        $this->manager->remove($pack);
        $this->manager->flush();
        return $this->redirectToRoute('listePack');
    }

    /**
     * @return Response
     * @Route("/entreprise/recapPack",name="recapPack")
     */
    public function recapPack():Response{

        return $this->render('entreprise/recapPack.html.twig');
    }

}