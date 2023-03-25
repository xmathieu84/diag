<?php

namespace App\Controller;

use App\Entity\EtatPrixOdi;
use App\Entity\FichierPrix;
use App\Entity\PackOdiPrixTaille;
use App\Entity\PrixOdiMission;
use App\Entity\PrixPrelevement;
use App\Repository\MissionOdiRepository;
use App\Repository\MissionRepository;
use App\Repository\PackOdiPrixTailleRepository;
use App\Repository\PackOdiRepository;
use App\Repository\PackRepository;
use App\Repository\PrelevementRepository;
use App\Repository\PrixOdiMissionRepository;
use App\Repository\PrixPrelevementRepository;
use App\Repository\SalarieRepository;
use App\Repository\TailleBienRepository;
use App\Repository\TypeBienRepository;
use App\Service\choixTemplate;
use App\Service\Fichier;
use App\Service\SalarieClassIndent;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TarifOdiController extends AbstractController
{

    /**
     * @param MissionOdiRepository $missionOdiRepository
     * @param EntityManagerInterface $manager
     * @param PrixOdiMissionRepository $prixOdiMissionRepository
     * @param RequestStack $requestStack
     * @param TailleBienRepository $tailleBienRepository
     * @param TypeBienRepository $typeBienRepository
     * @param PackOdiRepository $packOdiRepository
     * @param PackOdiPrixTailleRepository $packOdiPrixTailleRepository
     * @param \App\Repository\MissionRepository $missionRepository
     * @param PrelevementRepository $prelevementRepository
     * @param PrixPrelevementRepository $prixPprelevementRepository
     * @param \App\Repository\PackRepository $packRepository
     * @param SalarieRepository $salarieRepository
     * @param SalarieClassIndent $salarieClassIndent
     */
    public function __construct(private MissionOdiRepository $missionOdiRepository, private EntityManagerInterface $manager,
                                private PrixOdiMissionRepository $prixOdiMissionRepository, private RequestStack $requestStack,
                                private TailleBienRepository $tailleBienRepository,private TypeBienRepository $typeBienRepository,
                                private PackOdiRepository $packOdiRepository,private PackOdiPrixTailleRepository $packOdiPrixTailleRepository,
                                private MissionRepository $missionRepository, private PrelevementRepository $prelevementRepository,
                                private PrixPrelevementRepository $prixPprelevementRepository,private PackRepository $packRepository,
                                private SalarieRepository $salarieRepository,private SalarieClassIndent $salarieClassIndent)
    {
        $this->missionOdiRepository = $missionOdiRepository;
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->tailleBienRepository = $tailleBienRepository;
        $this->prixOdiMissionRepository = $prixOdiMissionRepository;
        $this->typeBienRepository = $typeBienRepository;
        $this->packOdiRepository = $packOdiRepository;
        $this->packOdiPrixTailleRepository = $packOdiPrixTailleRepository;
        $this->missionRepository = $missionRepository;
        $this->packRepository=$packRepository;
        $this->salarieRepository = $salarieRepository;
        $this->prixPprelevementRepository = $prixPprelevementRepository;
        $this->prelevementRepository = $prelevementRepository;
        $this->salarieClassIndent=$salarieClassIndent;
    }

    /**
     * @param choixTemplate $choixTemplate
     * @param null $id
     * @return Response
     * @Route("/entreprise/tarifOdi/{id}",name="tarifOdi")
     */
    public function voirTarif(choixTemplate $choixTemplate,$id=null):Response{
        $types = $this->typeBienRepository->findAll();
        $salarie=$this->salarieClassIndent->getSalarie($id);
        $tarif = $this->missionOdiRepository->findBy(['odi'=>$salarie],['mission'=>"ASC"]);
        $templateSalarie = $choixTemplate->templateAE($salarie->getEntreprise());
        $packs = $this->packOdiRepository->findBy(['odi'=>$salarie]);
        $prixMission = $this->prixOdiMissionRepository->findAll();
        $prixPaxk = $this->packOdiPrixTailleRepository->findAll();
        return $this->render('entreprise/tarifOdi.html.twig',[
            'types'=>$types,
            'details'=>$tarif,
            'template'=>$templateSalarie,
            'packs'=>$packs,
            'nombre'=>count($prixMission)+count($prixPaxk),
            'salarie'=>$salarie

        ]);
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/validerTarifOdi")
     */
    public function validerTarif():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);

        for ($i=0, $iMax = count($content->prixInter); $i< $iMax; $i++){
            $bien = $this->tailleBienRepository->find($content->bien[$i]);
            $mission = $this->missionOdiRepository->find($content->mission[$i]);
            $prix = $this->prixOdiMissionRepository->findOneBy(['taille'=>$bien,'missionOdi'=>$mission]);
            if ($content->prixInter[$i]===""){
                $content->prixInter[$i]=0;
            }
            if ($prix){
                $prix->setPrix($content->prixInter[$i]);
            }
            else{
                $prix = new PrixOdiMission();
                $prix->setPrix($content->prixInter[$i])
                    ->setMissionOdi($mission)
                    ->setTaille($bien);
            }

            $this->manager->persist($prix);
        }

        foreach ($content->prelev as $key=>$jValue) {
            $prelevement = $this->prelevementRepository->find($jValue);
            if ($content->prixPrelev[$key]===""){
                $prixPrelevement = 0;
            }
            else{
                $prixPrelevement = (float)$content->prixPrelev[$key];
            }
            $bien = $this->tailleBienRepository->find($content->bien[$key]);
            $prixP = new PrixPrelevement();
            $prixP->setPrix($prixPrelevement)
                ->setTaille($bien)
                ->setPrelevement($prelevement)
                ->setOdi($this->getUser()->getSalarie());
            $this->manager->persist($prixP);
        }
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @throws JsonException
     * @Route("/entreprise/validerPrixPack")
     */
    public function validerPrixPack():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        for ($i=0, $iMax = count($content->prix); $i< $iMax; $i++){
            $bien = $this->tailleBienRepository->find($content->bien[$i]);
            $packOdi = $this->packOdiRepository->find($content->pack[$i]);
            $prix = $this->packOdiPrixTailleRepository->findOneBy(['packOdi'=>$packOdi,'taille'=>$bien]);
            if ($content->prix[$i]===""){
                $content->prix[$i]=null;
            }
            if ($prix){
                $prix->setPrix($content->prix[$i]);
            }
            else{
                $prix = new PackOdiPrixTaille();
                $prix->setPrix($content->prix[$i])
                    ->setTaille($bien)
                    ->setPackOdi($packOdi);
            }
            $this->manager->persist($prix);
        }
        if ($content->moyen===1){
            $etat = new EtatPrixOdi();
            $etat->setEtat('non-prélevé')
                ->setPrix(60)
                ->setOdi($this->getUser()->getSalarie());
            $this->manager->persist($etat);
        }
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/retrouverTarifMission")
     */
    public function retrouverTarifMission():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $listePrixMission = [];
        for ($i=0, $iMax = count($content->bien); $i< $iMax; $i++){

            $bien = $this->tailleBienRepository->find($content->bien[$i]);
            $mission = $this->missionOdiRepository->find($content->mission[$i]);

            $prix = $this->prixOdiMissionRepository->findOneBy(['taille'=>$bien,'missionOdi'=>$mission]);
            if ($prix){
                $listePrixMission[]=$prix->getPrix();
            }

        }

        return new JsonResponse($listePrixMission);
    }

    /**
     * @return JsonResponse
     * @Route("/entreprise/retrouverPrixPrelevement")
     */
    public function retrouverPrixPrelevement():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $listePrix= [];
        foreach ($content->liste as $key=>$prelev){
            $bien = $this->tailleBienRepository->find($content->listeBien[$key]);
            $prelevement = $this->prelevementRepository->find($prelev);
            $prix = $this->prixPprelevementRepository->findOneBy(['taille'=>$bien,'prelevement'=>$prelevement,'odi'=>$this->getUser()->getSalarie()]);
            if ($prix){
                $listePrix[]=$prix->getPrix();
            }
            else{
                $listePrix[]=0;
            }
        }
        return new JsonResponse($listePrix);
    }
    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/retrouverPrixPack")
     */
    public function retrouverPrixPack():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $listePrixPack = [];
        for ($i=0, $iMax = count($content->bien); $i< $iMax; $i++){
            $bien = $this->tailleBienRepository->find($content->bien[$i]);
            $packOdi = $this->packOdiRepository->find($content->pack[$i]);
            $prix = $this->packOdiPrixTailleRepository->findOneBy(['packOdi'=>$packOdi,'taille'=>$bien]);
            if ($prix){
                $listePrixPack[]=$prix->getPrix();
            }

        }
        return new JsonResponse($listePrixPack);
    }

    /**
     * @param Fichier $fichier
     * @return JsonResponse
     * @Route("/entreprise/validerFilePrix")
     */
    public function validerFilePrix(Fichier $fichier):JsonResponse{
        $content = $this->requestStack->getCurrentRequest()->files;
        foreach ($content as $key=>$item){
            $filename = $fichier->saveFile('tarif'.$key,$this->getParameter("fichierPrix_directory"),$item);
            $fichierPrix = new FichierPrix();
            $fichierPrix->setNom($filename)
                ->setOdi($this->getUser()->getSalarie());
            $this->manager->persist($fichierPrix);
        }
        $etat = new EtatPrixOdi();
        $etat->setOdi($this->getUser()->getSalarie())
            ->setPrix(50)
            ->setEtat('non-prelevé');
        $this->manager->persist($etat);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @throws JsonException
     * @Route("/entreprise/prixMoyen")
     */
    public function prixMoyen():JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $listePrixMission = [];
        $listePrixPack=[];
        $listePrelev = [];
        foreach ($content->bien as $idBien){
            $bien = $this->tailleBienRepository->find($idBien);
            foreach ($content->mission as $idMission){
                $mission = $this->missionRepository->find($idMission);
                if (!in_array($mission, $bien->getMissionExclues()->toArray(), true)){
                    $prixMissions = $this->prixOdiMissionRepository->findByTailleMission($bien,$mission);
                    $total=0;
                    foreach ($prixMissions as $prixMission){
                        $total += $prixMission->getPrix();
                    }
                    if (count($prixMissions)===0){
                        $listePrixMission[]=0;
                    }
                    else{
                        $listePrixMission[]=number_format($total/count($prixMissions),2);
                    }
                }
            }
            foreach ($content->pack as $idPack){
                $pack = $this->packRepository->find($idPack);
                if (!in_array($pack, $bien->getPackExclu()->toArray(), true)){
                    $prixPacks = $this->packOdiPrixTailleRepository->findByPackTaille($pack,$bien);
                    $totalPack = 0;
                    foreach ($prixPacks as $prixPack){
                        $totalPack += $prixPack->getPrix();
                    }
                    if (count($prixPacks)===0){
                        $listePrixPack[]=0;
                    }
                    else{
                        $listePrixPack[]=$totalPack/count($prixPacks);
                    }
                }
            }
            foreach ($content->prelev as $prelevement){
                $prelev = $this->prelevementRepository->find($prelevement);
                $priPrelev = $this->prixPprelevementRepository->findBy(['prelevement'=>$prelev,'taille'=>$bien]);
                $total=0;
                foreach ($priPrelev as $prix){
                    $total += $prix->getPrix();
                }
                if (count($priPrelev)!==0){
                    $listePrelev[]= number_format($total/count($priPrelev),2);
                }
                else{
                    $listePrelev[]=0;
                }
            }
        }
        return (new JsonResponse())->setData([
            'moyenMission'=>$listePrixMission,
            'moyenPack'=>$listePrixPack,
            'prixPrelev'=>$listePrelev,
        ]);
    }
}