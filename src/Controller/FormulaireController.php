<?php

namespace App\Controller;

use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FormulaireController extends AbstractController
{
   use EntityManagerTrait,RequestTrait; 
    public function traitementForm($form,$objet,$route)
    {
        
        $formumlaire = $this->createForm($form,$objet,[
            'method'=>'POST'
        ]);
        if ($this->request->isMethod('POST'))
        {

           $formumlaire->handleRequest($this->request);


            if ($formumlaire->isSubmitted()&&$formumlaire->isValid())
                {
                    $this->manager->persist($objet);
                    $this->manager->flush();
                    return $this->redirectToRoute($route);                    
                }
           /* elseif ($formumlaire->isSubmitted() && !$formumlaire->isValid()) 
            {

            }*/
        } 
        return $formumlaire;
    }
}
