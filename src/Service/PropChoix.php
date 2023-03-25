<?php


namespace App\Service;


use App\Entity\Intervention;
use App\Entity\Proposition;
use App\Helper\EntityManagerTrait;
use App\Helper\ReservationRepoTrait;
use App\Repository\PourcentageRepository;

class PropChoix
{
    use ReservationRepoTrait,EntityManagerTrait;

    /**
     * @var Geoloc
     */
    private Geoloc $geoloc;
    /**
     * @var Mail
     */
    private Mail $mail;
    /**
     * @var PourcentageRepository
     */
    private PourcentageRepository $pourcentageRepository;

    /**
     * PropChoix constructor.
     * @param Geoloc $geoloc
     * @param Mail $mail
     * @param PourcentageRepository $pourcentageRepository
     */
    public function __construct(Geoloc $geoloc,Mail $mail,PourcentageRepository $pourcentageRepository)
    {
        $this->mail=$mail;
        $this->geoloc=$geoloc;
        $this->pourcentageRepository=$pourcentageRepository;
    }

    /**
     * @param Intervention $intervention
     * @param Proposition $proposition
     */
    public function traitementProposition(Intervention $intervention,Proposition $proposition){
        $OTD = $proposition->getSalarie();
        $reservation = $this->reservationRepository->findOneBy(['intervention' => $intervention]);

        $duree = $this->geoloc->tempsTrajet(
            $intervention->getAdresse()->getCoordonnees()->getLatitude(),
            $intervention->getAdresse()->getCoordonnees()->getLongitude(),
            $OTD->getAdresse()->getCoordonnees()->getLatitude(),
            $OTD->getAdresse()->getCoordonnees()->getLongitude()
        );
        $rdv = $intervention->getRdvAT();
        $RDV = $rdv->getTimestamp();
        $time = $RDV - $duree;
        $fin = (new \DateTimeImmutable())->setTimestamp($time);
        $props = $intervention->getPropositions();
        foreach ($props as $prop) {
            if ($prop != $proposition) {
                $this->mail->mailPropNonChoisie($prop->getSalarie()->getUser()->getEmail());
            }
        }
        $reservation->setDebut($fin)
            ->setSalarie($OTD);
        $this->mail->mailPropChoisie($proposition->getSalarie()->getUser()->getEmail());


        $intervention->setPrix($proposition->getPrix() + $proposition->getIndemnite())
            ->setStatuInter('Intervention validée')
            ->setPropositionChoisie($proposition);
        $this->manager->persist($intervention);
        $this->manager->persist($reservation);
    }
}