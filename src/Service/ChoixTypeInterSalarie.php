<?php

namespace App\Service;

use App\Entity\DetailPrix;
use App\Entity\MissionOdi;
use App\Entity\TauxHoraire;
use App\Entity\User;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\ListeInterTypeInterRepoTrait;
use App\Helper\TauxHoraireRepoTrait;
use App\Repository\DetailPrixRepository;
use App\Repository\MissionOdiRepository;
use App\Repository\MissionRepository;

/**
 * Class ChoixTypeInterSalarie
 * @package App\Service
 */
class ChoixTypeInterSalarie
{
    use RequestTrait, EntityManagerTrait, SalarieRepoTrait, ListeInterTypeInterRepoTrait, TauxHoraireRepoTrait;
    private MissionRepository $missionRepository;
    private MissionOdiRepository $missionOdiRepository;

    /**
     * @param MissionRepository $missionRepository
     * @param MissionOdiRepository $missionOdiRepository
     */
    public function __construct(MissionRepository $missionRepository,MissionOdiRepository $missionOdiRepository)
    {
        $this->missionRepository = $missionRepository;
        $this->missionOdiRepository = $missionOdiRepository;
    }

    /**
     * @param $id
     * @param $user
     * @return void
     */
    public function ajouter($id, $user,$type):void
    {
        $OTD = $this->salarieRepository->find($user);
        if ($type==='otd'){
            $listeInterTypeInter = $this->listeInterTypeInterRepository->findOneById($id);
            $tauxHoraire = new TauxHoraire();
            $tauxHoraire->setSalarie($OTD)
                ->setInter($listeInterTypeInter);
            $this->manager->persist($tauxHoraire);
            $this->manager->flush();
        }
        if($type==="odi"){
            $mission = $this->missionRepository->find($id);
            $detailPrix = new MissionOdi();
            $detailPrix->setMission($mission)
                ->setOdi($OTD);
            $this->manager->persist($detailPrix);
            $this->manager->flush();
        }


    }

    /**
     * @param $id
     * @param $iduser
     */
    public function retirer($id, $iduser,$type):void
    {

        $OTD = $this->salarieRepository->findOneBy(['id'=>$iduser]);
        if ($type==='otd'){
            $listeInterTypeInter = $this->listeInterTypeInterRepository->findOneById($id);
            $tauxHoraire = $this->tauxHoraireRepository->findOneBy(['salarie' => $OTD, 'inter' => $listeInterTypeInter]);
            $this->manager->remove($tauxHoraire);

        }
        if ($type==='odi'){
            $mission = $this->missionRepository->find($id);
            $details = $this->missionOdiRepository->findBy(['odi'=>$OTD,'mission'=>$mission]);
            foreach ($details as $detail){
                foreach ($detail->getPrixOdiMissions() as $prixOdiMission){
                    $this->manager->remove($prixOdiMission);
                }
                $this->manager->remove($detail);
            }

        }
        $this->manager->flush();

    }
}
