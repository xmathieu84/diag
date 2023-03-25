<?php

namespace App\Helper;

use App\Repository\InterventionRepository;

trait InterRepoTrait
{

    protected InterventionRepository $interventionRepository;
    /**
     * @required
     *
     * @param InterventionRepository $interventionRepository
     * @return void
     */
    public function setInterventionRepo(InterventionRepository $interventionRepository)
    {
        $this->interventionRepository = $interventionRepository;
    }
}
