<?php
namespace App\Helper;
use App\Repository\DemandeurRepository;

trait DemandeurRepoTrait
{
    /**
     * @var DemandeurRepository
     */
    protected DemandeurRepository $demandeurRepository;

    /**
     * @param DemandeurRepository $demandeurRepository
     * @required
     * @return void
     */
    public function setDroneRepo(DemandeurRepository $demandeurRepository)
    {
        $this->demandeurRepository=$demandeurRepository;
    }
}