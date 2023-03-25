<?php

namespace App\Helper;

use App\Repository\RapportRepository;

trait RapportRepoTrait{

    protected RapportRepository $rapportRepository;
    /**
     * @required
     */

    public function setRapportRepo(RapportRepository $rapportRepository)
    {
        $this->rapportRepository=$rapportRepository;
    }
}