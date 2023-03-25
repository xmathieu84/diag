<?php

namespace App\Controller;

use App\Entity\Coordonnees;
use App\Entity\Miltaire;
use App\Form\ArmeeType;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\MiltaireRepository;
use App\Service\DefinirAcces;
use App\Service\Geoloc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminArmeeController extends AbstractController
{
    use EntityManagerTrait,RequestTrait;
    /**
     * @Route("/administrateur/ajouterArmee", name="ajouterArmee")
     */
    public function index(DefinirAcces $definirAcces,Geoloc $geoloc)
    {
        $militaire = new Miltaire();
        $form = $this->createForm(ArmeeType::class,$militaire);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()){
            $militaire->getUser()->setPassword($definirAcces->identPass($militaire));
            $militaire->getUser()->setRoles(["ROLE_MILITAIRE"]);
            $coordonnees = new Coordonnees();
            $position = $geoloc->geolocalisation($militaire);
            $coordonnees->setLatitude($position[0])
                ->setLongitude($position[1])
                ->setAdresse($militaire->getAdresse());
            $this->manager->persist($militaire);
            $this->manager->persist($coordonnees);
            $this->manager->flush();
            return $this->redirectToRoute('listeArmee');
        }
        return $this->render('admin_armee/ajouter.html.twig', [
                'form'=>$form->createView()
        ]);
    }

    /**
     * @param MiltaireRepository $miltaireRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/administrateur/listeArmee",name="listeArmee")
     */
    public function listeBase(MiltaireRepository $miltaireRepository){
        $bases = $miltaireRepository->findAll();
        return $this->render('admin_armee/liste.html.twig',['bases'=>$bases]);
    }
}
