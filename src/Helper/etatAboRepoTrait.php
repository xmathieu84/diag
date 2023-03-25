<?php

namespace App\Helper;

use App\Repository\EtatAbonnementRepository;

trait etatAboRepoTrait
{

    protected EtatAbonnementRepository $etatAbonnementRepository;

    /**
     * @required
     *
     * @param EtatAbonnementRepository $etatAbonnementRepository
     * @return void
     */
    public function setEtatAboRepo(EtatAbonnementRepository $etatAbonnementRepository)
    {
        $this->etatAbonnementRepository = $etatAbonnementRepository;
    }
}
