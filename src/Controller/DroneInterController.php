<?php

namespace App\Controller;

use App\Entity\Drone;

use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\DroneRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\choixTemplate;
use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DroneInterController
 * @package App\Controller
 */
class DroneInterController extends AbstractController
{
    use EntityManagerTrait, RequestTrait, DroneRepoTrait, InterRepoTrait,EntrepriseRepoTrait,SalarieRepoTrait;


    /**
     * @Route("/drone/enregistrer", name="enregistrerDrone")
     * 
     * @isGranted("ROLE_ENTREPRISE")
     *
     * @return JsonResponse
     */
    public function enregistrerDrone(): JsonResponse
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);

        $drone = new Drone();
        if ($this->request->get('vitesse') ==""){
            $vitesse = null;
        } else{
            $vitesse = $this->request->get('vitesse');
        }
        if ($this->request->get('marque') ==""){
            $marque = null;
        } else{
            $marque = $this->request->get('marque');
        }
        $drone->setNomFabriquant($this->request->get('fabriquant'))
            ->setTypeDrone($this->request->get('type'))
            ->setnumeroDgac($this->request->get('numero'))
            ->setPoidDrone((float)$this->request->get('poids'))
            ->setCaptif($this->request->get('captif'))
            ->setClasse($this->request->get('classe'))
            ->setTrame($this->request->get('trame'))
            ->setMarqueCEE($marque)
            ->setSerial($this->request->get('serial'))
            ->setVitesse($vitesse)
            ->setActif(true)
            ->setEntrepris($salarie->getEntreprise());
        $this->manager->persist($drone);
        $this->manager->flush();

        $reponse = new JsonResponse();

        return $reponse->setData($drone->getPoidDrone());
    }

    /**
     * @Route("/drone/listeDrones",name="listedrone")
     * @isGranted("ROLE_ENTREPRISE")
     *
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function listedrone(choixTemplate $choixTemplate): Response
    {

        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $drone = $this->droneRepository->findBy(['entrepris' => $salarie->getEntreprise(),'actif'=>true]);

        return $this->render('drone/listeDrone.html.twig', [

            'drones' => $drone
        ]);
    }



    /**
     * @Route("/droneChoix",name="droneChoix")
     * @isGranted("ROLE_SALARIE")
     *
     * @return JsonResponse
     */
    public function droneChoix(): JsonResponse
    {


        $contenu = $this->request->getContent();
        $Contenu = json_decode($contenu, true);
        $inter = $Contenu['idinter'];
        $idDrone = $Contenu['drone'];
        $intervention = $this->interventionRepository->findOneBy(['id' => $inter]);
        $drone = $this->droneRepository->findOneBy(['id' => $idDrone]);
        $intervention->setDrone($drone);
        $this->manager->persist($intervention);
        $this->manager->flush();

        $reponse = new JsonResponse();
        return $reponse->setData();
    }

    /**
     * @Route("/drone/supprimerDrone/{id}",name="supprimerDrone")
     * @isGranted("ROLE_ENTREPRISE")
     *
     * @param integer $id
     * @return RedirectResponse
     */
    public function supprimerDrone($id): RedirectResponse
    {

        $drone = $this->droneRepository->findOneBy(['id' => $id]);
        $drone->setActif(false);
        $this->manager->persist($drone);
        $this->manager->flush();
        return $this->redirectToRoute('listedrone');
    }

    /**
     *  @Route("/droneInter")
     *  @isGranted("ROLE_SALARIE")
     *
     * @return JsonResponse
     */
    public function carteInter(): JsonResponse
    {

        $contenu = $this->request->getContent();
        $intervention = $this->interventionRepository->findOneBy(['id' => $contenu]);
        $latitude = $intervention->getAdresse()->getCoordonnees()->getLatitude();
        $longitude = $intervention->getAdresse()->getCoordonnees()->getLongitude();
        $response = new JsonResponse();

        return $response->setData(['lat' => $latitude, 'lng' => $longitude]);
    }

    /**
     *  @Route("/choixDroneInter")
     * @isGranted("ROLE_SALARIE")
     *
     * @return JsonResponse
     */
    function choixDroneInter(): JsonResponse
    {
        $contenu = json_decode($this->request->getContent(), true);
        $drone = $this->droneRepository->findOneBy(['id' => $contenu['idDrone']]);
        $intervention = $this->interventionRepository->findOneBy(['id' => $contenu['idInter']]);
        $intervention->setDrone($drone);
        $this->manager->persist($intervention);
        $this->manager->flush();
        $reponse = new JsonResponse();
        return $reponse->setData('ok');
    }
}
