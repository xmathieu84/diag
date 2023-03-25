<?php

namespace App\Service;

use App\Entity\Demandeur;
use App\Entity\Intervention;
use App\Repository\ComuComMailingRepository;
use App\Repository\InterventionRepository;
use App\Repository\ReservationRepository;
use DateTime;
use DateTimeZone;

/**
 * Class InterRegionDep
 * @package App\Service
 */
class InterRegionDep
{


    /**
     * @var ReservationRepository $reservationRepository
     */
    private ReservationRepository $reservationRepository;
    /**
     * @var InterventionRepository
     */
    private InterventionRepository $interventionRepository;
    /**
     * @var ComuComMailingRepository
     */
     private ComuComMailingRepository $comuComMailingRepository;
    /**
     * InterRegionDep constructor.
     * @param ReservationRepository $reservationRepository
     * @param InterventionRepository $interventionRepository
     */
    public function __construct(ReservationRepository $reservationRepository, InterventionRepository $interventionRepository,ComuComMailingRepository $comuComMailingRepository)
    {

        $this->reservationRepository = $reservationRepository;
        $this->comuComMailingRepository= $comuComMailingRepository;
        $this->interventionRepository = $interventionRepository;
    }

    /**
     * @param Intervention $intervention
     * @return array
     */
    public function dispoOTD(Intervention $intervention):array
    {
        $reservation = $intervention->getReservation();
        $otd = $reservation->getSalarie();
        $result = [];
        $dateDebut = $reservation->getDebut();
        $dateDepart = $reservation->getDepart();
        $dateLimiteAm = $reservation->getDebut()->setTime(8, 0, 0);
        $dateMilieu = $reservation->getDebut()->setTime(12, 0);
        if ($reservation->getDepart()){
            $dateLimitePm = $reservation->getDepart()->setTime(19, 0);
        }
        else{
            $dateLimitePm = $reservation->getDebut()->setTime(19, 0);
        }


        if ($dateDepart && $dateDebut) {
            if ($dateDebut < $dateMilieu && $dateDepart < $dateMilieu) {
                $resas = $this->reservationRepository->findResaVide($dateMilieu, $dateLimitePm, $otd);

                if (empty($resas)) {
                    $result = [
                        'result' => true,
                        'date' => $reservation->getDepart()->setTime(15, 0, 0)->format('d-m-y H:i')
                    ];
                }

            } elseif ($dateDebut >= $dateMilieu) {
                $resas = $this->reservationRepository->findResaVide($dateLimiteAm, $dateMilieu, $otd);

                if (empty($resas)) {
                    $result = [
                        'result' => true,
                        'date' => $reservation->getDepart()->setTime(9, 0, 0)->format('d-m-y H:i')
                    ];
                }

            } elseif ($dateDepart >= $dateMilieu && $dateDebut < $dateMilieu) {
                $result = [
                    'result' => false,
                    'date' => null
                ];
            }
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function interventionProche(Demandeur $institution, DateTime $date=null){
        $listeInter = [];
        if (!$date){
            $date = new DateTime('NOW',new DateTimeZone('Europe/Paris'));
        }
        if ($institution->getProfil() ==='Communaute de communes'){
            $com = $this->comuComMailingRepository->findByNomRentre($institution->getNom());
            foreach ($com->getVille() as $ville){
                $interventions = $this->interventionRepository->findForInsti(substr($ville,6),$date);
                foreach ($interventions as $intervention){
                    $listeInter[]=$intervention;
                }
            }
        }
        else{
            $interventions = $this->interventionRepository->findForInsti($institution->getAdresse()->getVille(),new \DateTime('NOW',new \DateTimeZone('Europe/Paris')));
            $listeInter = $interventions;
        }
        return $listeInter;
    }
}
