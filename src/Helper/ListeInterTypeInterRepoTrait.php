<?php

namespace App\Helper;

use App\Repository\ListeInterTypeInterRepository;

trait ListeInterTypeInterRepoTrait
{

    protected $listeInterTypeInterRepository;

    /**
     * @required
     *
     * @param ListeInterTypeInterRepository $listeInterTypeInterRepository
     * @return void
     */
    public function setListeInterTypeInterRepo(ListeInterTypeInterRepository $listeInterTypeInterRepository)
    {
        $this->listeInterTypeInterRepository = $listeInterTypeInterRepository;
    }
}
