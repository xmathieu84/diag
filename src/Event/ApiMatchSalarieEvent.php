<?php


namespace App\Event;


use Symfony\Contracts\EventDispatcher\Event;

class ApiMatchSalarieEvent extends Event
{
    public const MATCH = 'matchOtd.event';
}