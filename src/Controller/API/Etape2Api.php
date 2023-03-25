<?php


namespace App\Controller\API;


use App\Entity\Intervention;
use App\Entity\Reservation;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Etape2Api
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }

    public function __invoke(Intervention $data):Intervention
    {
        $date = new DateTime();
        $data->setRdvAT($date->setTimestamp($data->getDateInter()/1000));
        $reservation = new Reservation();
        $reservation->setIntervention($data);
        $this->entityManager->persist($data);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
        return $data;
    }
}