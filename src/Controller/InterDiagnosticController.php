<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\InterDiag;
use App\Entity\User;
use App\Event\AdressEvent;
use App\Form\AdresseInterType;
use App\Repository\InterDiagRepository;
use App\Repository\TailleBienRepository;
use App\Repository\TypeBienRepository;
use App\Service\choixTemplate;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterDiagnosticController extends AbstractController
{
    public function __construct(private TypeBienRepository $typeBienRepository,
                                private RequestStack $requestStack,private choixTemplate $choixTemplate,
                                private TailleBienRepository $tailleBienRepository,private EntityManagerInterface $manager,
                                private InterDiagRepository $interDiagRepository)
    {
        $this->typeBienRepository = $typeBienRepository;
        $this->choixTemplate = $choixTemplate;
        $this->tailleBienRepository=$tailleBienRepository;
        $this->manager=$manager;
        $this->interDiagRepository=$interDiagRepository;


    }

    /**
     *
     * @param string|null $identifiant
     * @return Response
     * @Route("/createDiag/{identifiant}",name="createDiag")
     */
    public function createDiag(string $identifiant=null):Response{

        $template = $this->choixTemplate->templateDiag($this->getUser());
        $types = $this->typeBienRepository->findAll();
        $inter = ($identifiant) ? $this->interDiagRepository->findOneBy(['identifiat'=>$identifiant]) : null;
        return $this->render('intervention/createDiag.html.twig',[
            'template'=>$template,
            'types'=>$types,
            'inter'=>$inter
        ]);

    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/recupDiag/{identifaint}")
     */
    public function recupDiag(string $identifaint):JsonResponse{
        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$identifaint]);
        $liste=[];
        $date = ($inter->getDateRdv()) ? $inter->getDateRdv()->format('Y-m-d') : null;
        $bien = $inter->getTailleBien()->getTypeBien();
        foreach ($bien->getTaille() as $taille){
            $liste['taille']['taille'][]=$taille->getTaille();
            $liste['taille']['id'][]=$taille->getId();
        }
        $liste["rdv"] = $date;
        $liste["type"]=$inter->getTypeDiag();
        $liste["bienChoisi"]=$bien->getId();
        $liste["tailleChoisie"]=$inter->getTailleBien()->getId();
        return new JsonResponse($liste);
    }

    /**
     * @return JsonResponse
     * @Route("/saveDiag/{id}")
     * @throws JsonException
     */
    public function saveDiag(string $id=null):JsonResponse{
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $taille = $this->tailleBienRepository->find($content->tailleBien);
        $demandeur=null;
        if ($content->dateRdv!==""){
            $date = \DateTime::createFromFormat("Y-m-d",$content->dateRdv);
        }
        else{
            $date=null;
        }
        if ($this->getUser()){
            if ($this->getUser()->hasRole('ROLE_AGENT')){
                $demandeur = $this->getUser()->getAgent()->getDemandeur();
            }
            else{
                $demandeur = $this->getUser()->getDemandeur();
            }
        }


        $inter = ($id) ? $this->interDiagRepository->findOneBy(['identifiat'=>$id]) : new InterDiag();
        $inter->setTailleBien($taille)
            ->setDateRdv($date)
            ->setDemandeur($demandeur)
            ->setTypeDiag($content->type);
        $this->manager->persist($inter);
        $this->manager->flush();
        return new JsonResponse($inter->getIdentifiat());
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/recupeTaille/{id}")
     */
    public function recupeTaille(int $id):JsonResponse{
        $type = $this->typeBienRepository->find($id);
        $liste=[];
        foreach ($type->getTaille() as $taille){
            $liste[]= ['taille'=>$taille->getTaille(),'id'=>$taille->getId()];
        }
        return new JsonResponse($liste);
    }


    /**
     * @return Response
     * @Route("/etape1-precision/{identifiant}")
     */
    public function saveTypeDiag(string $identifiant):Response{
        $template = $this->choixTemplate->templateDiag($this->getUser());
        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$identifiant]);
        return $this->render('intervention/etape2Diag.html.twig',[
            'template'=>$template,
            'inter'=>$inter
        ]);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @Route("/recupeEtape2/{id}")
     */
    public function recupEtape2(string $id):JsonResponse{
        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$id]);
        $liste=[];
        $liste['permis'] = $inter->getPermis();
        $liste['elec'] = $inter->getAgeElec();
        $liste['gaz']= $inter->getAgeGaz();
        return new JsonResponse($liste);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/completeDiag/{identifiant}")
     * @throws JsonException
     */
    public function completeDiag(string $identifiant):JsonResponse{
        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$identifiant]);
        $content = json_decode($this->requestStack->getCurrentRequest()->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $inter->setAgeElec($content->ageElec)
            ->setAgeGaz($content->ageGaz)
            ->setAmiante($content->amiante)
            ->setGaz($content->gaz)
            ->setElectricite($content->elec)
            ->setPlomb($content->plomb)
            ->setPermis($content->permis);
       $this->manager->persist($inter);
       $this->manager->flush();
        return new JsonResponse($identifiant);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/etape2-adresse/{id}")
     */
    public function adresseDiag(string $id,EventDispatcherInterface $dispatcher):Response{

        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$id]);
        if ($inter->getAdresse()){
            $adresse = $inter->getAdresse();
        }
        else{
            $adresse = new Adresse();
        }
        $template = $this->choixTemplate->templateDiag($this->getUser());

        $form = $this->createForm(AdresseInterType::class,$adresse,['user'=>$this->getUser()]);
        if (!$this->getUser()){
            $adresseFact =null;
        }
        elseif ($this->getUser()->hasRole('ROLE_AGENT')){
            $adresseFact = $this->getUser()->getAgent()->getDemandeur()->getAdresse();
        }
        else{
            $adresseFact = $this->getUser()->getDemandeur()->getAdresse();
        }
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $inter->setAdresse($adresse);
        if ($form->isSubmitted() && $form->isValid()){
            $event = new GenericEvent($inter);
            $dispatcher->dispatch($event,  AdressEvent::NAME);
            $this->manager->persist($adresse);
            $this->manager->flush();
            return $this->redirectToRoute("resultatInter",[
                'id'=>$inter->getIdentifiat()
            ]);

        }
        return $this->renderForm("intervention/adresseDiag.html.twig",[
            'template'=>$template,
            'form'=>$form,
            "adresseFact"=>$adresseFact
        ]);
    }



}