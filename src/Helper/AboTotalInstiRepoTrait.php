<?php

namespace App\Helper;

use App\Repository\AboTotalInstiRepository;

trait AboTotalInstiRepoTrait{

    protected AboTotalInstiRepository $aboTotalInstiRepository;

    /**
     * @param AboTotalInstiRepository $aboTotalInstiRepository
     * @required
     */
    public function setAboTotalInstiRepo(AboTotalInstiRepository $aboTotalInstiRepository):void{

        $this->aboTotalInstiRepository = $aboTotalInstiRepository;
    }
}
