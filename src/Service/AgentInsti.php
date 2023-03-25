<?php


namespace App\Service;


use App\Entity\Agent;
use App\Entity\Demandeur;
use App\Helper\AboTotalInstiRepoTrait;
use App\Repository\PackSupAboInstiRepository;

class AgentInsti
{
    private PackSupAboInstiRepository $packSupAboInstiRepository;
    private DefinirDate $definirDate;
    use AboTotalInstiRepoTrait;
    public function __construct(PackSupAboInstiRepository $packSupAboInstiRepository, DefinirDate $definirDate)
    {
        $this->definirDate = $definirDate;
        $this->packSupAboInstiRepository = $packSupAboInstiRepository;
    }

    public function nombreAgent(Demandeur $demandeur)
    {
        $packAbos = $this->packSupAboInstiRepository->findByDemandeur($demandeur, $this->definirDate->aujourdhuiImmutable());
        $abonnement = $this->aboTotalInstiRepository->findAbonnement($demandeur,$this->definirDate->aujourdhuiImmutable());
        $maxAgent = 0;
        foreach ($packAbos as $packAbo) {
            $maxAgent += $packAbo->getPackSup()->getEmploye();
        }
        foreach ($abonnement as $state){
            $maxAgent += $state->getAbonnement()->getUtlisateur();
        }

        return $maxAgent ;
    }

    public function agantActuel(Agent $agent ){
        $nombreAgent =0;
        foreach ($agent->getDemandeur()->getAgents() as $agentI){
            $nombreAgent ++;
            foreach ($agentI->getResponsable() as $responsable){
                $nombreAgent= $responsable->getChef()->count() +$nombreAgent;
            }
        }
        return $nombreAgent;
    }
}