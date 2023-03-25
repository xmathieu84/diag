<?php


namespace App\Controller\API;


use App\Entity\Intervention;
use App\Event\AdressEvent;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


class Etape3Api
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $entityManager,EventDispatcherInterface $dispatcher)
    {
        $this->entityManager=$entityManager;
        $this->dispatcher=$dispatcher;
    }

    public function __invoke(Intervention $data):Intervention
    {
        $event = new GenericEvent($data);
        $this->dispatcher->dispatch($event,AdressEvent::NAME);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}