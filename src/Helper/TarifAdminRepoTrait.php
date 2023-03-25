<?php

namespace App\Helper;

use App\Repository\TarifAdminRepository;

trait TarifAdminRepoTrait{

    protected $tarifAdminRepository;

    /**
     * @required
     */

    public function setTarifAdminRepo(TarifAdminRepository $tarifAdminRepository)
    {
        $this->tarifAdminRepository=$tarifAdminRepository;
    }
}