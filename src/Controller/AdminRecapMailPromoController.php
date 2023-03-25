<?php

namespace App\Controller;

use App\Repository\CampagneMailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AdminRecapMailPromoController extends AbstractController
{
    /**
     * @param CampagneMailRepository $campagneMailRepository
     * @return Response
     * @Route("/administrateur/listeMailPromo",name="listeMailPromo")
     */
    public function listeMailPromo(CampagneMailRepository $campagneMailRepository):Response{
        $campagnes = $campagneMailRepository->findForListe();
        return $this->render('administrateur/listeCampagneMail.html.twig',[
            'campagnes'=>$campagnes
        ]);
    }

    /**
     * @param CampagneMailRepository $campagneMailRepository
     * @param int $id
     * @return JsonResponse
     * @Route("/administrateur/recupListeMail/{id}")
     */
    public function recupListeMail(CampagneMailRepository $campagneMailRepository,int $id):JsonResponse{
        $campagne = $campagneMailRepository->find($id);
        $response  =new JsonResponse();
        $response->setData([
            'titre'=>$campagne->getTitreEmail(),
            'contenu'=>$campagne->getContenu(),
            'codePromo'=>$campagne->getCodePromo()
        ]);
        return $response;
    }
}