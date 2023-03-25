<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartenaireController extends AbstractController
{
    /**
     * @param string $nom
     * @return Response
     * @Route("/partenaire/assurance/{nom}",name="partAssurance")
     */
    public function assurance(string $nom):Response{

        return $this->render('partenaire/assurance.html.twig',[
            'nom'=>$nom
        ]);
    }
}