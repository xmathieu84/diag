<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NotifEvent extends Event
{
    public const NAME = 'notif';

}