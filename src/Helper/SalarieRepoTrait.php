<?php

namespace App\Helper;

use App\Entity\Mission;
use App\Repository\SalarieRepository;

trait SalarieRepoTrait{
    /**
     * @var SalarieRepository
     */
    protected SalarieRepository $salarieRepository;
    /**
     * @required
     *
     * @param SalarieRepository $salarieRepository
     * @return void
     */
    public function setSalarieRepository(SalarieRepository $salarieRepository)
    {
        $this->salarieRepository=$salarieRepository;
    }


}