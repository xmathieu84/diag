<?php

namespace App\Controller;

use App\Entity\AbonnementGci;
use App\Entity\Abonnements;
use App\Entity\PackSup;
use App\Form\AbonnementsType;
use App\Form\PackType;
use App\Helper\AboRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\PackSupRepository;
use App\Service\DefinirDate;
use MangoPay\Libraries\HttpResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminAbonnementController
 * @package App\Controller
 */
class AdminAbonnementController extends AbstractController
{

    use EntityManagerTrait, RequestTrait, AboRepoTrait, etatAboRepoTrait, EntrepriseRepoTrait;

    /**
     * @param null or integer $id
     * @return RedirectResponse|Response
     *
     * @Route("/administrateur/abonnement", name="admin_abonnement")
     * @Route("/administrateur/abonnememnt/{id}",name="modifer_abonnement")
     * Definition des abonnements par l'administrateur
     */
    public function definirAbonnement($id = null)
    {


        $Abonnements = $this->abonnementsRepository->findAll();


        return $this->render('administrateur/abonnement.html.twig', [

            'abonnements' => $Abonnements
        ]);
    }

    /**
     * @param PackSupRepository $packSupRepository
     * @param null $id
     * @return Response
     * @Route("/adinistrateur/listePack/{id}",name="listePack")
     */
    public function listePack(PackSupRepository $packSupRepository, $id = null)
    {

        $packs = $packSupRepository->findAll();
        if ($id) {
            $pack = $packSupRepository->findOneBy(['id' => $id]);
        } else {
            $pack = new PackSup();
        }
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($pack);
            $this->manager->flush();
            return $this->redirectToRoute('listePack');
        }
        return $this->render('administrateur/packSup.html.twig', [
            'packs' => $packs,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/administrateur/changeElementAbonnement")
     */
    public function changeElementAbonnement(){
        $content = json_decode($this->request->getContent());

        $abonnement = $this->abonnementsRepository->findOneBy(['id'=>$content->id]);
        switch ($content->type){
            case 'nom' :
                $abonnement->setNom($content->valeur);
                break;
            case  'prixAbo':
                $abonnement->setPrix($content->valeur);
                break;
            case "prixOtdSup":
                $abonnement->setOtdSup($content->valeur);
                break;
            case 'otdMax':
                $abonnement->setOtdMax($content->valeur);
                break;
        }
        $this->manager->persist($abonnement);
        $this->manager->flush();
        return new JsonResponse();

    }


}
