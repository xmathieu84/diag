<?php


namespace App\Event;


use Symfony\Contracts\EventDispatcher\Event;

class MangoPayUserEvent extends Event
{
    public const MANGO = 'mangoPay.event';
}