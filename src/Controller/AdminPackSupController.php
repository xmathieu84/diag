<?php

namespace App\Controller;

use App\Entity\PackSup;
use App\Helper\EntityManagerTrait;
use App\Helper\PackSupRepoTrait;
use App\Helper\RequestTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPackSupController extends AbstractController
{
    use PackSupRepoTrait,EntityManagerTrait,RequestTrait;

    /**
     * @param $type
     * @param $profil
     * @return Response
     * @Route("/administrateur/packSupplementaire/{type}/{profil}",name="packSupplementaire")
     */
    public function packSupGc($type,$profil):Response{

        $packs = $this->packSupRepository->findBy(['cible'=>$type,'profil'=>$profil]);

        return $this->render("administrateur/packSupGc.html.twig",[
            'packs'=>$packs,
            'type'=>$type,
            'profil'=>$profil
        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/administrateur/newPAckGc")
     */
    public function newPAckGc():JsonResponse{
        $content = json_decode($this->request->getContent());
        $pack = new PackSup();
        $pack->setProfil($content->profil)
            ->setPrix($content->prix)
            ->setCible($content->type)
            ->setEmploye($content->user)
            ->setNom($content->nom);

        $this->manager->persist($pack);
        $this->manager->flush();
        return  new JsonResponse();

    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/modifPack")
     */
    public function modifPack():JsonResponse{
        $content = json_decode($this->request->getContent());
        $pack = $this->packSupRepository->findOneBy(['id'=>$content->id]);
        switch ($content->typeChange){
            case "nom":
                $pack->setNom($content->valeur);
                break;
            case "prixPack":
                $pack->setPrix($content->valeur);
                break;
            case "userPack":
                $pack->setEmploye($content->valeur);
                break;
        }

        $this->manager->persist($pack);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param $profil
     * @param $limiteH
     * @param $limiteB
     * @return Response
     * @Route("/administrateur/packSupInsti/{profil}/{limiteH}/{limiteB}",name="packSupinsti")
     */
    public function packSupInsti($profil,$limiteH,$limiteB):Response{
        $pack = $this->packSupRepository->findByHabitant($profil,$limiteH,$limiteB);

        return $this->render("administrateur/packSupInsti.html.twig",[
                'profil'=>$profil,
                'limiteH'=>$limiteH,
                'limiteB'=>$limiteB,
                'packs'=>$pack
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/newPackInsti")
     */
    public function newPackInsti(){
        $content= json_decode($this->request->getContent());
        $pack = new PackSup();
        $pack->setEmploye($content->user)
            ->setNom($content->nom)
            ->setPrix($content->prix)
            ->setCible('institution')
            ->setProfil($content->profil)
            ->setLimiteB($content->limiteB)
            ->setLimiteH($content->limiteH);
        $this->manager->persist($pack);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route ("/administrateur/changePackInsti")
     */
    public function changePackInsti():JsonResponse{
        $content = json_decode($this->request->getContent());
        $pack = $this->packSupRepository->findOneBy(['id'=>$content->id]);
        switch ($content->typeChange){
            case "nom":
                $pack->setNom($content->valeur);
                break;
            case "prixPack":
                $pack->setPrix($content->valeur);
                break;
            case "userPack":
                $pack->setEmploye($content->valeur);
                break;
            case "limiteBPack":
                $pack->setLimiteB($content->valeur);
                break;
            case "limiteHPack":
                $pack->setLimiteH($content->valeur);
                break;
        }

        $this->manager->persist($pack);
        $this->manager->flush();
        return new JsonResponse();
    }
}