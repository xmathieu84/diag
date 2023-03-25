<?php


namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Salarie;
use App\Helper\InterRepoTrait;
use App\Helper\ReservationRepoTrait;


/**
 * Class InterRepo
 * @package App\Service
 */
class InterRepo
{
    use InterRepoTrait, ReservationRepoTrait;

    /**
     * @param string $statutInter
     * @param Salarie $salarie
     * @return Reservation[]
     */
    public function interSalarie(string $statutInter,Salarie $salarie)
    {
        $intervention = $this->interventionRepository->findBy(['statuInter' => $statutInter], ['rdvAT' => 'ASC']);
        $reservation = $this->reservationRepository->findBy(['salarie' => $salarie, 'intervention' => $intervention]);

        return $reservation;
    }

    /**
     * @param string $statutInter
     * @param Demandeur $demandeur
     * @return Reservation[]
     */
    public function interDemandeur(string $statutInter,Demandeur $demandeur)
    {
        $intervention = $this->interventionRepository->findBy(['statuInter' => $statutInter], ['rdvAT' => 'DESC']);
        $reservation = $this->reservationRepository->findBy(['salarie' => $demandeur, 'intervention' => $intervention]);

        return $reservation;
    }
}
