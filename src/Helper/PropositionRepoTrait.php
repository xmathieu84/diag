<?php

namespace App\Helper;

use App\Repository\PropositionRepository;

trait PropositionRepoTrait
{

    protected PropositionRepository $propositionRepository;

    /**
     * @required
     *
     * @param PropositionRepository $propositionRepository
     * @return void
     */
    public function setPropositionRepo(PropositionRepository $propositionRepository)
    {
        $this->propositionRepository = $propositionRepository;
    }
}
