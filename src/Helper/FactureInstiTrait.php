<?php

namespace App\Helper;


use App\Repository\FactureInstiRepository;

trait FactureInstiTrait
{
    /**
     * @var FactureInstiRepository
     */
    protected FactureInstiRepository $factureInstiRepository;

    /**
     * @param FactureInstiRepository $factureInstiRepository
     * @required
     */
    public function setRepoFactureInsti(FactureInstiRepository $factureInstiRepository)
    {
        $this->factureInstiRepository = $factureInstiRepository;
    }
}
