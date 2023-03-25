<?php

namespace App\Helper;

use App\Repository\PhotoRepository;

trait PhotoRepoTrait{
    protected $photoRepository;
    /**
     * @required
     *
     * @param PhotoRepository $photoRepository
     * @return void
     */
    public function setPhotoRepo(PhotoRepository $photoRepository)
    {
        $this->photoRepository=$photoRepository;
    }
}