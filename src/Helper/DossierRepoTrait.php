<?php
namespace App\Helper;

use App\Repository\DossierRepository;

trait DossierRepoTrait{

    protected DossierRepository $dossierRepository;

    /**
     * @param DossierRepository $dossierRepository
     * @required
     */
    public function setDossierRepo(DossierRepository $dossierRepository){
        $this->dossierRepository=$dossierRepository;
    }
}