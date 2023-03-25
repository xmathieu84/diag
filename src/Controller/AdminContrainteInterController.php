<?php

namespace App\Controller;

use App\Entity\ContrainteInter;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\ContrainteInterRepository;
use App\Service\DefinirDate;
use App\Service\Mail;
use App\Service\SmsFactor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminContrainteInterController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    use InterRepoTrait,RequestTrait,EntityManagerTrait;
    /**
     * @return Response
     * @Route("/administrateur/contrainte",name="contrainteInter")
     */
    public function contraintInter(DefinirDate $definirDate):Response{
        $interventions = $this->interventionRepository->findForContrainte($definirDate->aujourdhui());
        return $this->render('administrateur/contrainte.html.twig',[
            'interventions'=>$interventions
        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/administrateur/ajoutContraintInter/{id}")
     */
    public function ajoutContraintInter(int $id,Mail $mail,SmsFactor $smsFactor):JsonResponse{
        $content = json_decode($this->request->getContent());
        $intervention = $this->interventionRepository->findOneBy(['id'=>$id]);

        foreach ($content->content as $item){

            $contrainte = new ContrainteInter();
            if ($item->speci ===""){
                $speci = null;
            }
            else{
                $speci = $item->speci;
            }
           $contrainte->setIntervention($intervention)
                ->setDistance($item->alt)
               ->setSpecificite($speci)
                ->setType($item->type);
            $this->manager->persist($contrainte);
            $this->manager->flush();
        }

       if (count($intervention->getContrainteInters())!==0){
            foreach ($intervention->getPropositions() as $proposition){
                $mail->mailDemandeIntervention($proposition->getSalarie()->getUser()->getEmail());
                $smsFactor->smsInter($proposition->getSalarie()->getTelephone()->getNumero());
            }
        }

        return new JsonResponse();
    }

    /**
     * @param int $id
     * @return Response
     * @Route ("/administrateur/voirContrainte/{id}",name="voirContrainte")
     */
    public function voirContrainte(int $id):Response{
        $inter = $this->interventionRepository->findOneBy(['id'=>$id]);
        return $this->render('administrateur/voirContrainte.html.twig',[
            'intervention'=>$inter
        ]);
    }

    /**
     * @param $id
     * @param ContrainteInterRepository $repository
     * @return JsonResponse
     * @Route("/administrateur/supprimerContrainte/{id}")
     */
    public function supprimerContrainte($id,ContrainteInterRepository $repository):JsonResponse{
        $contrainte = $repository->findOneBy(['id'=>$id]);
        $this->manager->remove($contrainte);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @param ContrainteInterRepository $repository
     * @return JsonResponse
     * @Route("/administrateur/modifierContrainte/{id}")
     */
    public function modifierContrainte(int $id,ContrainteInterRepository $repository):JsonResponse{
        $contrainte = $repository->findOneBy(['id'=>$id]);
        $content = json_decode($this->request->getContent());
        if ($content->modif ==="type"){
            $contrainte->setType($content->valeur);
        }
        if ($content->modif ==='altitude'){
            $contrainte->setDistance($content->valeur);
        }
        if ($content->modif ==='specificite'){
            $contrainte->setSpecificite($content->valeur);
        }
        $this->manager->persist($contrainte);
        $this->manager->flush();
        return new JsonResponse();
    }
}