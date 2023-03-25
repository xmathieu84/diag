<?php

namespace App\Helper;


use App\Repository\AmbassadeurRepository;

trait AmbassadeurRepoTrait{
    protected AmbassadeurRepository $ambassadeurRepository;

    /**
     * @param AmbassadeurRepository $ambassadeurRepository
     * @required
     */
    public function setAmbassadeurRepo(AmbassadeurRepository $ambassadeurRepository):void{
        $this->ambassadeurRepository = $ambassadeurRepository;
    }
}
