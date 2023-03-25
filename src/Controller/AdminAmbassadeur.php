<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminAmbassadeur extends AbstractController
{
    public function creerAmbbassadeurOtd():Response{
        return $this->render('administrateur/ambassadeurOtd.html.twig');
    }
}