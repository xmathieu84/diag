<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;


class UserConnectListener
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager=$manager;
    }

    public function __invoke(LoginSuccessEvent $event)
    {
        $user = $event->getUser();

    }
}