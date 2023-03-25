<?php


namespace App\Event;


use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    public const NAME = 'user.access';

}