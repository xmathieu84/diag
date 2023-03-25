<?php

namespace App\Helper;

use App\Repository\AbonnementGciRepository;

trait AbonnementGcirepoTrait{

    protected AbonnementGciRepository $abonnementGciRepository;

    /**
     * @param AbonnementGciRepository $abonnementGciRepository
     * @required
     */
    public function setAboGcirepo (AbonnementGciRepository $abonnementGciRepository):void{
       $this->abonnementGciRepository=$abonnementGciRepository;
    }
}
