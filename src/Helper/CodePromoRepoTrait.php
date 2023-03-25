<?php

namespace App\Helper;

use App\Repository\CodePromoRepository;

/**
 * 
 */
trait CodePromoRepoTrait
{
    protected CodePromoRepository $codePromoRepository;

    /**
     * @required
     *
     * @param CodePromoRepository $codePromoRepository
     * @return void
     */
    public function setCodePromo(CodePromoRepository $codePromoRepository)
    {
        $this->codePromoRepository = $codePromoRepository;
    }
}
