<?php

namespace App\Helper;

use App\Repository\TypInterRepository;

trait TypeInterRepoTrait
{
    /**
     * @var TypInterRepository
     */
    protected TypInterRepository $typInterRepository;

    /**
     * @required
     *
     * @param TypInterRepository $typInterRepository
     * @return void
     */
    public function setTypeInterRepo(TypInterRepository $typInterRepository)
    {
        $this->typInterRepository = $typInterRepository;
    }
}
