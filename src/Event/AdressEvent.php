<?php


namespace App\Event;

use App\Entity\Adresse;
use App\Entity\Entreprise;
use Symfony\Contracts\EventDispatcher\Event;

class AdressEvent extends Event
{
    public const NAME = 'address.coordonnee';

    /**
     * @var Adresse
     */
    protected $adresse;

    /**
     * AdressEvent constructor.
     * @param Entreprise $entreprise
     */
    public function __construct(Entreprise $entreprise)
    {
         $this->$entreprise = $entreprise;
    }


    /**
     * @return Entreprise
     */
    public function getAddress():Entreprise
    {
        return $this->entreprise;
    }
}