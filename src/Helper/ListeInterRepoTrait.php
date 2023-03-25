<?php

namespace App\Helper;

use App\Repository\ListeInterRepository;

trait ListeInterRepoTrait
{
    /**
     * @var ListeInterRepository
     */
    protected ListeInterRepository $listeInterRepository;

    /**
     * @required
     *
     * @param ListeInterRepository $listeInterRepository
     * @return void
     */
    public function setListeInterRepo(ListeInterRepository $listeInterRepository)
    {
        $this->listeInterRepository = $listeInterRepository;
    }
}
