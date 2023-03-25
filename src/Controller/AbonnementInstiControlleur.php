<?php

namespace App\Controller;

use App\Entity\AbonnementGci;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementInstiControlleur extends AbstractController
{
    use AbonnementGcirepoTrait,RequestTrait,EntityManagerTrait;

    /**
     * @return Response
     * @Route("/administrateur/abonnementInsti",name="abonnementInstitution")
     */
    public function abonnementInstitution():Response{
        $abonnement = $this->abonnementGciRepository->findBy(['cible'=>'institutionnel']);
        return $this->render('administrateur/abonnementinstitution.html.twig',[
            'abonnements'=>$abonnement
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/changeAbonnementInsti")
     */
    public function changeAbonnementInsti():JsonResponse{
        $content = json_decode($this->request->getContent());
        $abonnement = $this->abonnementGciRepository->findOneBy(['id'=>$content->id]);
        switch ($content->type){
            case 'nom' :
                $abonnement->setNom($content->valeur);
                break;
            case  'prixAbo':
                $abonnement->setPrix($content->valeur);
                break;
            case "profilAbo":
                $abonnement->setProfil($content->valeur);
                break;
            case 'limiteB':
                $abonnement->setLimiteB($content->valeur);
                break;
            case "limiteH":
                $abonnement->setLimiteH($content->valeur);
                break;
            case 'userAbo':
                $abonnement->setUtlisateur($content->valeur);
                break;
        }
        $this->manager->persist($abonnement);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route ("/administrateur/newAbonnementInsti")
     */
    public function newAbonnementInsti():JsonResponse{
        $content = json_decode($this->request->getContent());

        $abonnement = new AbonnementGci();
        $abonnement->setNom($content->nom)
            ->setUtlisateur($content->user)
            ->setLimiteH($content->limiteHaute)
            ->setLimiteB($content->limiteBasse)
            ->setCible('institutionnel')
            ->setPrix($content->prix)
            ->setProfil($content->profil);

       $this->manager->persist($abonnement);
        $this->manager->flush();
        return  new JsonResponse();
    }


}