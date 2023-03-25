<?php

namespace App\Controller;

use App\Entity\AbonnementGci;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminAbonnementGc extends AbstractController
{
use AbonnementGcirepoTrait,RequestTrait,EntityManagerTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/administrateur/abonnementGc",name="abonnementGc")
     */
    public function adminAbonnementGc(){
        $abonnement = $this->abonnementGciRepository->findBy(['cible'=>'gc'],['profil'=>'ASC']);

        return $this->render('administrateur/abonnementgc.html.twig',[
            'abonnements'=>$abonnement
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/modifAbonneGc")
     */
    public function modifAbonneGc(){
        $content = json_decode($this->request->getContent());
        $abonnement = $this->abonnementGciRepository->findOneBy(['id'=>$content->id]);

        if ($content->typeChange ==='nom'){
            $abonnement->setNom($content->valeur);
        }
        elseif ($content->typeChange ==="prixAbo"){
            $abonnement->setPrix($content->valeur);
        }
        elseif ($content->typeChange ==="userAbo"){
            $abonnement->setUtlisateur($content->valeur);
        }
        elseif ($content->typeChange ==="dureeAbo"){
            $abonnement->setDuree(New \DateInterval($content->valeur));
        }
        $this->manager->persist($abonnement);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/creerNouvelAbonnement")
     */
    public function creerNouvelAbonnement():JsonResponse{
        $content = json_decode($this->request->getContent());
        $abonnement = new AbonnementGci();
        $testAbonnement = $this->abonnementGciRepository->findOneBy(['profil'=>$content->profil,'utlisateur'=>$content->user]);
        $response = new JsonResponse();
        if (!$testAbonnement){
            $abonnement->setPrix($content->prixAbo)
                ->setNom($content->nom)
                ->setCible('gc')
                ->setLimiteB(0)
                ->setLimiteH(0)
                ->setProfil($content->profil)
                ->setUtlisateur($content->user);

            $this->manager->persist($abonnement);
             $this->manager->flush();
            return $response->setData('ok');
        }
        else{
            return $response->setData("L'abonnement existe déjà");
        }



    }
}