<?php


namespace App\Event;
use \Symfony\Contracts\EventDispatcher\Event;

class DemandeurEvent extends Event
{

    public const NAME = 'demandeurMango.event';
}