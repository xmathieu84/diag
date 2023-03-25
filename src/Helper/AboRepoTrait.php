<?php

namespace App\Helper;

use App\Repository\AbonnementsRepository;


trait AboRepoTrait{

    /**
     * @var AbonnementsRepository
     */
    protected AbonnementsRepository $abonnementsRepository;
    /**
     * @required
     *
     * @param AbonnementsRepository $abonnementsRepository
     * @return void
     */
    public function setAboRepo(AbonnementsRepository $abonnementsRepository):void
    {
        $this->abonnementsRepository=$abonnementsRepository;
    }
}