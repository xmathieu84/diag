<?php

namespace App\Helper;

use App\Repository\TauxHoraireRepository;

trait TauxHoraireRepoTrait
{

    protected $tauxHoraireRepository;

    /**
     * @required
     *
     * @param TauxHoraireRepository $tauxHoraireRepository
     * @return void
     */
    public function setTauxHoraireRepo(TauxHoraireRepository $tauxHoraireRepository)
    {
        $this->tauxHoraireRepository = $tauxHoraireRepository;
    }
}
