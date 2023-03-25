<?php

namespace App\Controller;

use App\Entity\AboTotalInsti;
use App\Entity\ProBtp;
use App\Form\ProBtpType;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use App\Service\FacturePdfService;
use App\Service\Geoloc;
use Mpdf\MpdfException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AjoutAbonnementController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    use AbonnementGcirepoTrait,EntityManagerTrait,AgentRepoTrait,RequestTrait;

    /**
     * @param string $type
     * @param Geoloc $geoloc
     * @param DefinirDate $definirDate
     * @param FacturePdfService $facturePdfService
     * @return Response
     * @throws MpdfException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @Route("/ajouterAbonnement/{type}",name="ajouterAbonnement")
     * @Security("is_granted('ROLE_BTP') or is_granted('ROLE_MANITOU')")
     */
    public function changerAbonnement( string $type,Geoloc $geoloc,DefinirDate $definirDate,FacturePdfService $facturePdfService):Response{

        $abonnement = $this->abonnementGciRepository->findBy(['profil'=>$type]);
        $pro = new ProBtp();
        $form = $this->createForm(ProBtpType::class,$pro);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()){
            $pro->setDemandeur($agent->getDemandeur());
            foreach ($form['travaux']->getData() as $travaux){
                $pro->addTravaux($travaux);
            }
            if ($form['villeDepart']->getData()){
                $lieu = $geoloc->localise($form['villeDepart']->getData);
            }
            else{
                $lieu = $geoloc->localise($agent->getDemandeur()->getAdresse()->getNumero().' '
                    .$agent->getDemandeur()->getAdresse()->getNomVoie().' '.$agent->getDemandeur()->getAdresse()->getCodePostal().' '.$agent->getDemandeur()->getAdresse()->getVille());
            }
            $coordonnees = $geoloc->distance($lieu[0],$lieu[1],$form["distanceInter"]->getData());
            $agent->getDemandeur()->getAdresse()->getCoordonnees()->setLatitude($lieu[0])
                ->setLongitude($lieu[1])
                ->setLatMaxInter($coordonnees[0])
                ->setLatMaxInter($coordonnees[1])
                ->setLonMinInter($coordonnees[2])
                ->setLonMaxInter($coordonnees[3]);
            $etat = new AboTotalInsti();
            
            $etat->setDemandeur($agent->getDemandeur())
                ->setTotal($form['abonnement']->getData()->getPrix())
                ->setAbonne(true)
                ->setAbonnement($form['abonnement']->getData())
                ->setDebut($definirDate->aujourdhuiImmutable())
                ->setFin($definirDate->aujourdhuiImmutable()->add($form['abonnement']->getData()->getDuree()));

            $agent->getUser()->addRole('ROLE_BTP');

            $this->manager->persist($pro);
            $this->manager->persist($etat);
            $facturePdfService->factureAbonnementGci($agent,$etat);
            return $this->redirectToRoute('validerNewAbo');

        }
        return $this->render('institution/ajouterAbonnement.html.twig',[
            'type'=>$type,
            'abonnements'=>$abonnement,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/alerteAjoutAbonnement/{id}")
     * @Security("is_granted('ROLE_BTP')")
     */
    public function alerteAjoutAbonnement(int $id):Response{
        $abonnement = $this->abonnementGciRepository->findOneBy(['id'=>$id]);
        return $this->render('institution/alerteAjoutAbonnement.html.twig',[
            'abonnement'=>$abonnement,
            'idAbo'=>$id
        ]);
    }

    /**
     * @param int $id
     * @param DefinirDate $definirDate
     * @param FacturePdfService $facturePdfService
     * @return RedirectResponse
     * @throws MpdfException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @Route("/validerAjout/{id}",name="validerAjout")
     * @Security("is_granted('ROLE_BTP') or is_granted('ROLE_MANITOU')")
     */
    public function validerAjout(int $id,DefinirDate $definirDate,FacturePdfService $facturePdfService):RedirectResponse{
        $abonnement = $this->abonnementGciRepository->findOneBy(['id'=>$id]);
        $user = $this->getUser();
        $etat = new AboTotalInsti();
        $etat->setAbonne(true)
            ->setDemandeur($this->getUser()->getAgent()->getDemandeur())
            ->setDebut($definirDate->aujourdhuiImmutable())
            ->setFin($definirDate->aujourdhuiImmutable()->add($abonnement->getDuree()))
            ->setAbonnement($abonnement)
            ->setTotal($abonnement->getPrix()*1.2);
        $facturePdfService->factureAbonnementGci($this->getUser()->getAgent(),$etat);
        $user->addRole('ROLE_GRANDCOMPTE');
        $user->addRole('ROLE_MANITOU');
        $this->manager->persist($etat);
        $this->manager->persist($user);
        $this->manager->flush();
        return $this->redirectToRoute("validerNewAbo");
    }

    /**
     * @return Response
     * @Route("/validerNouvelAbonnement",name="validerNewAbo")
     */
    public function nouvelAbonnmentValide():Response{

        return $this->render('institution/nouvelAbonnementValider.html.twig');
    }

    /**
     * @return JsonResponse
     * @Route("/recupereAdresse")
     * @Security(" is_granted('ROLE_MANITOU')")
     */
    public function recupereAdresse():JsonResponse{
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);

        $adresse = $agent->getDemandeur()->getAdresse()->getNumero().' '.$agent->getDemandeur()->getAdresse()->getNomVoie().' '.$agent->getDemandeur()->getAdresse()->getCodePostal().' '.$agent->getDemandeur()->getAdresse()->getVille();
        return new JsonResponse($adresse);

    }
}