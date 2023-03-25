<?php

namespace App\Helper;

use App\Repository\DossierGeneralRepository;

trait DossierGenRepoTrait{

    protected DossierGeneralRepository $dossierGeneralRepository;

    /**
     * @param DossierGeneralRepository $dossierGeneralRepository
     * @required
     */
    public function setDossierGenRepo(DossierGeneralRepository $dossierGeneralRepository){
        $this->dossierGeneralRepository=$dossierGeneralRepository;
    }
}
