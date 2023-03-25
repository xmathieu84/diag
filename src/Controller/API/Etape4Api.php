<?php


namespace App\Controller\API;


use App\Entity\Intervention;

use App\Event\ApiMatchSalarieEvent;
use App\Service\Fichier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class Etape4Api extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $dispatcher;
    private Fichier $fichier;


    public function __construct(EntityManagerInterface $entityManager,EventDispatcherInterface $dispatcher,Fichier $fichier)
    {
        $this->entityManager=$entityManager;
        $this->dispatcher=$dispatcher;
        $this->fichier=$fichier;

    }

    public function __invoke(Intervention $data)
    {
        foreach ($data->getPhotoInter() as $photo){
            $realPhoto = $this->fichier->decodeBase64($photo->getPhotoBase64(),$this->getParameter('photos_directory'));

            $photo->setNom($realPhoto);
            $this->entityManager->persist($photo);

        }
        $this->entityManager->persist($data);
        $event = new GenericEvent($data);
        $this->dispatcher->dispatch($event,ApiMatchSalarieEvent::MATCH);

        $this->entityManager->flush();


        return $data;
    }
}