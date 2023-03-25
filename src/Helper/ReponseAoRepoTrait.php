<?php

namespace App\Helper;


use App\Repository\ReponseAoRepository;

trait ReponseAoRepoTrait{
    protected ReponseAoRepository $reponseAoRepository;

    /**
     * @param ReponseAoRepository $reponseAoRepository
     * @required
     */
    public function setReponseAoRepo(ReponseAoRepository $reponseAoRepository):void{
        $this->reponseAoRepository = $reponseAoRepository;
    }
}