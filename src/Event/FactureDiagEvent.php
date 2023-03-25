<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class FactureDiagEvent extends Event
{
    public const NAME = 'factureDiag.event';
}