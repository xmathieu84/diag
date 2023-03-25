<?php

namespace App\Helper;

use App\Repository\BanqueRepository;

trait BanqueRepoTrait{

    protected BanqueRepository $banqueRepository;

    /**
     * @param BanqueRepository $banqueRepository
     * @required
     */
    public function setBanqueRepo(BanqueRepository $banqueRepository){
        $this->banqueRepository=$banqueRepository;
    }
}
