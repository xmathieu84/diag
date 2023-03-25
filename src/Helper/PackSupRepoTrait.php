<?php

namespace App\Helper;


use App\Repository\PackSupRepository;

trait PackSupRepoTrait{

    protected PackSupRepository $packSupRepository;

    /**
     * @param PackSupRepository $packSupRepository
     * @required
     */
    public function setPackSupRepo(PackSupRepository $packSupRepository){
        $this->packSupRepository=$packSupRepository;
    }
}