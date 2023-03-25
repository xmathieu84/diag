<?php

namespace App\Controller;


use App\Entity\RcComplement;
use App\Form\RcPro2Type;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\RcComplementRepository;
use App\Service\Fichier;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssuranceController extends AbstractController
{
    use RequestTrait,EntityManagerTrait;


    /**
     * @param Fichier $fichier
     * @return Response
     * @Route("/entreprise/voirAssurance",name="voirAssu")
     * @isGranted ("ROLE_ENTREPRISE")
     */
    public function voirAssurance(Fichier $fichier):Response{
        $entreprise = $this->getUser()->getSalarie()->getEntreprise();
        $complementaire = new RcComplement();
        $form = $this->createForm(RcPro2Type::class,$complementaire);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()){
            $assuranceComplemanteire = $form['fichier']->getData();
            $nom = $fichier->moveFile($assuranceComplemanteire,$this->getParameter('assuance_directory'),'assurance');
            $complementaire->setFichier($nom);
            $complementaire->setAssurance($entreprise->getEntAss());
            $this->manager->persist($complementaire);
            $this->manager->flush();

            return $this->redirectToRoute("voirAssu");
        }

        return $this->render('entreprise/voirAssurance.html.twig',[
            'entreprise'=>$entreprise,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @param string $type
     * @param Fichier $fichier
     * @return JsonResponse
     * @Route ("/changeAssurancePrincipale/{type}")
     * @isGranted ("ROLE_ENTREPRISE")
     */
    public function changeAssurancePrincipale(string $type,Fichier $fichier):JsonResponse{
        $assurance = $this->getUser()->getSalarie()->getEntreprise()->getEntAss();
        if ($type ==="compagnie"){
            $content = $this->request->getContent();
            $assurance->setNomCompagnie($content);
        }
        elseif ($type ==="contrat"){
            $content = $this->request->getContent();
            $assurance->setAssPro($content);
        }
        elseif ($type ==="attestation"){
            $content = $this->request->files->get('attestation');

           $nom = $fichier->moveFile($content,$this->assurancesDirectory,'assurance');
            $assurance->setAssProFichier($nom);
        }
        $this->manager->persist($assurance);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param string $type
     * @param Fichier $fichier
     * @return JsonResponse
     * @Route("/chnageAssuranceComplementaire/{type}")
     * @isGranted ("ROLE_ENTREPRISE")
     */
    public function chnageAssuranceComplementaire(string $type,Fichier $fichier):JsonResponse{
        $complement = $this->getUser()->getSalarie()->getEntreprise()->getEntAss()->getRcComplement();
        if ($type ==="compagnie"){
            $content = $this->request->getContent();
            $complement->setCompagnie($content);
        }
        elseif ($type ==="contrat"){
            $content = $this->request->getContent();
            $complement->setNumero($content);
        }
        elseif ($type ==="attestation"){
            $content = $this->request->files->get('attestation');

            $nom = $fichier->moveFile($content,$this->assurancesDirectory,'assurance');
            $complement->setFichier($nom);
        }
        $this->manager->persist($complement);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param $id
     * @param RcComplementRepository $repository
     * @return JsonResponse
     * @Route("/supprimerRcComplement/{id}")
     * @isGranted ("ROLE_ENTREPRISE")
     */
    public function supprimerRcComplement($id,RcComplementRepository $repository):JsonResponse{
        $complement = $repository->findOneBy(['id'=>$id]);
        $this->manager->remove($complement);
        $this->manager->flush();
        return new JsonResponse();

    }
}