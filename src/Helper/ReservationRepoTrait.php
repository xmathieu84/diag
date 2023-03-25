<?php

namespace App\Helper;

use App\Repository\ReservationRepository;

trait ReservationRepoTrait{
    /**
     * 
     *
     * @var ReservationRepository
     */
    protected $reservationRepository;

    /**
     * @required
     *
     * @param ReservationRepository $reservationRepository
     * @return void
     */
    public function setRevervationRepo(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository=$reservationRepository;
    }
}