<?php

namespace App\Helper;

use App\Repository\UserRepository;

trait UserRepoTrait{

    protected UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     * @required
     */
    public function setUserRepo(UserRepository $userRepository){

        $this->userRepository = $userRepository;
    }
}
