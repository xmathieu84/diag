<?php

namespace App\Helper;

use App\Repository\EntrepriseRepository;

trait EntrepriseRepoTrait{

    /**
     * @var EntrepriseRepository
     */
    protected EntrepriseRepository $entrepriseRepository;
    /**
     * @required
     *
     * @param EntrepriseRepository $entrepriseRepository
     * @return void
     */
    public function setEntrepriseRepo(EntrepriseRepository $entrepriseRepository)
    {
        $this->entrepriseRepository=$entrepriseRepository;
    }
}