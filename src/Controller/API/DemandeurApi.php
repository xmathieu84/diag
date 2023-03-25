<?php

namespace App\Controller\API;

use App\Entity\Demandeur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DemandeurApi extends AbstractController
{

    public function __construct()
    {
    }

    /**
     * @return Demandeur
     *
     */
    public function __invoke():Demandeur
    {
        return $this->getUser()->getDemandeur();
    }
}