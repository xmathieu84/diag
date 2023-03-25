<?php


namespace App\Controller;


use App\Event\ApiMatchSalarieEvent;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\ReservationRepoTrait;
use App\Helper\SalarieRepoTrait;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChangerDateInterController extends AbstractController
{
    use RequestTrait,InterRepoTrait,SalarieRepoTrait,ReservationRepoTrait,EntityManagerTrait;

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     * @Route ("/changerDateInter/{id}")
     */
    public function nombreOtd(int $id):JsonResponse{
        $content = $this->request->getContent();
        $date = new \DateTime('',new \DateTimeZone('Europe/Paris'));
        $reservation = $this->reservationRepository->findOneBy(['id'=>$id]);
        $date->setTimestamp((int)$content /1000);
        $salaries = $this->salarieRepository->choixSalariePourIntervention($reservation->getIntervention()->getListeInter()->getId(), $reservation->getIntervention()->getTypeInter()->getId(),$reservation->getIntervention()->getId(),$date,$reservation->getIntervention()->getAdresse()->getCoordonnees()->getLatitude(),$reservation->getIntervention()->getAdresse()->getCoordonnees()->getLongitude());

        return new JsonResponse(count($salaries));

    }

    /**
     * @param int $id
     * @param EventDispatcherInterface $dispatcher
     * @throws Exception
     * @Route("/validerDateInter/{id}")
     */
    public function changeDateInter(int $id,EventDispatcherInterface $dispatcher){
        $content = $this->request->getContent();
        $date = new \DateTime('',new \DateTimeZone('Europe/Paris'));
        $reservation = $this->reservationRepository->findOneBy(['id'=>$id]);
        $date->setTimestamp((int)$content /1000);
        $reservation->getIntervention()->setRdvAT($date);
        $this->manager->persist($reservation);
        $this->manager->flush();
        $event = new GenericEvent($reservation->getIntervention());
        $dispatcher->dispatch($event,ApiMatchSalarieEvent::MATCH);
        exit();

    }
}