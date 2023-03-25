<?php

namespace App\Helper;

use App\Repository\DroneRepository;

trait DroneRepoTrait{

    protected $droneRepository;
    /**
     * @required
     *
     * @param DroneRepository $droneRepository
     * @return void
     */
    public function setDroneRepo(DroneRepository $droneRepository)
    {
        $this->droneRepository=$droneRepository;
    }
}