<?php

namespace App\Controller;

use App\Entity\Ambassadeur;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\AboRepoTrait;
use App\Helper\AmbassadeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAmbassadeurController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController{
        use AmbassadeurRepoTrait,EntityManagerTrait,RequestTrait,AboRepoTrait,AboRepoTrait,AbonnementGcirepoTrait;

    /**
     * @return Response
     * @Route("/administrateur/ambasssadeur",name="ambassadeur")
     */
        public function listeCodeAmbassadeur():Response{
            $amabassadeurs = $this->ambassadeurRepository->findAll();
            $abonnements = $this->abonnementsRepository->findAll();
            return $this->render("administrateur/listeAmbassadeur.html.twig",[
                'ambassadeurs'=>$amabassadeurs,
                'abonnements'=>$abonnements
            ]);
}

    /**
     * @return JsonResponse
     * @Route ("/administrateur/ambassadeurOtd")
     */
    public function creerAbassadeurOtd():JsonResponse{
            $content = json_decode($this->request->getContent());
            $duree = new \DateInterval('P'.$content->duree.'M');
            $abonnement = $this->abonnementsRepository->findOneBy(['id'=>$content->profil]);
            $ambassadeur = new Ambassadeur();
            $ambassadeur->setAbonnementOtd($abonnement)
                ->setCodeReduc($content->code)
                ->setPrix($content->prix)
                ->setProfil('otd')
                ->setActif(true)
                ->setCommentaire($content->com)
                ->setDatedebut(\DateTime::createFromFormat('Y-m-d',$content->dateDebut))
                ->setDatefin(\DateTime::createFromFormat('Y-m-d',$content->dateFin))
                ->setDureeAbo($duree)
                ->setMaximum($content->nbreMax);
            $this->manager->persist($ambassadeur);
            $this->manager->flush();
            return new JsonResponse();

    }

    /**
     * @return JsonResponse
     * @throws \Exception
     * @Route ("/administrateur/ambassadeurGc")
     */
    public function creerAmbassadeurGc():JsonResponse{
        $content = json_decode($this->request->getContent());
        $duree = new \DateInterval('P'.$content->duree.'M');
        $abonnements = $this->abonnementGciRepository->findBy(['profil'=>$content->profil]);
        foreach ($abonnements as $key=>$abonnement){
            $ambassadeur = new Ambassadeur();
            $ambassadeur->setAbonnementGci($abonnement)
                ->setCodeReduc($content->code.$key)
                ->setPrix($content->prix)
                ->setProfil($content->profil)
                ->setActif(true)
                ->setCommentaire($content->com)
                ->setDatedebut(\DateTime::createFromFormat('Y-m-d',$content->dateDebut))
                ->setDatefin(\DateTime::createFromFormat('Y-m-d',$content->dateFin))
                ->setDureeAbo($duree)
                ->setMaximum($content->nbreMax);
            $this->manager->persist($ambassadeur);
        }
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route ("/administrateur/ambassadeurInsti")
     */
    public function creerAmbassadeurInsti():JsonResponse{
        $content = json_decode($this->request->getContent());
        $duree = new \DateInterval('P'.$content->duree.'M');
        if (intval($content->profil)===0){
            $abonnement = $this->abonnementGciRepository->findOneBy(['profil'=>$content->profil]);
            $profil = $content->type;

        }
        else{
            $abonnement = $this->abonnementGciRepository->abonnementInsti($content->profil,'insti');
            $profil = 'insti';
        }
        $ambassadeur = new Ambassadeur();
        $ambassadeur->setAbonnementGci($abonnement)
            ->setCodeReduc($content->code)
            ->setPrix($content->prix)
            ->setProfil($profil)
            ->setActif(true)
            ->setCommentaire($content->com)
            ->setDatedebut(\DateTime::createFromFormat('Y-m-d',$content->dateDebut))
            ->setDatefin(\DateTime::createFromFormat('Y-m-d',$content->dateFin))
            ->setDureeAbo($duree)
            ->setMaximum($content->nbreMax);
        $this->manager->persist($ambassadeur);
        $this->manager->flush();
        return new JsonResponse();
    }



}