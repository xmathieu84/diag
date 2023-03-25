<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    private EntityManagerInterface $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


  public function __invoke(LogoutEvent $event)
  {
      $user = $event->getToken()->getUser();
      $user->setIsConnect(false);
      $this->manager->persist($user);
      $this->manager->flush();
  }

}