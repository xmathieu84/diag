<?php

namespace App\Helper;

use App\Repository\SujetRepository;

trait SujetRepoTrait
{
    protected $sujetRepository;

    /**
     * @required
     *
     * @param SujetRepository $sujetRepository
     * @return void
     */
    public function setSujetRepo(SujetRepository $sujetRepository)
    {
        $this->sujetRepository = $sujetRepository;
    }
}
