<?php

namespace App\Controller;

use App\Entity\EtatAbonnement;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\choixTemplate;
use App\Service\codeActivation;
use App\Service\DefinirAcces;
use App\Service\DefinirDate;
use App\Service\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class LimiteOTDController extends AbstractController
{
    use etatAboRepoTrait, EntrepriseRepoTrait, EntityManagerTrait,SalarieRepoTrait;


    /**
     *  @Route("/limite/OTD", name="limiteOTD")
     * @isGranted("ROLE_ENTREPRISE")
     * 
     * @return RedirectResponse
     */
    public function testLimite(): RedirectResponse
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $etat = $this->etatAbonnementRepository->findOneBy(['entreprise' => $entreprise, 'abonne' => true]);
        if ($etat->getAbonnement()->getOtdMax() < count($entreprise->getSalaries())) {
            return $this->redirectToRoute('rappelMontant');
        } else {
            return $this->redirectToRoute('entreprise');
        }
    }

    /**
     * @Route("/rappelMontant" , name="rappelMontant")
     *@isGranted("ROLE_ENTREPRISE")
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     */
    public function rappelMontantAbonnement(choixTemplate $choixTemplate, DefinirDate $definirDate)
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $template = $choixTemplate->templateAE($entreprise);
        $etat = $this->etatAbonnementRepository->findOneBy(['entreprise' => $entreprise, 'abonne' => true]);
        $abonnement = $etat->getAbonnement();
        $nouveauMontant = $abonnement->getPrix() + ($abonnement->getOtdSup() * (count($entreprise->getSalaries()) - $abonnement->getOtdMax()));
        return $this->render('entreprise/rappelMontant.html.twig', [
            'template' => $template[0],
            'montant' => $nouveauMontant,
            'etat' => $etat
        ]);
    }

    /**
     * @Route("/valider/abonnement/{id}",name="validerAbonnement")
     *@isGranted("ROLE_ENTREPRISE")
     * @param [type] $id
     * @param DefinirDate $definirDate
     * @return RedirectResponse
     */
    public function validerAbonnement($id, DefinirDate $definirDate, Mail $mail, codeActivation $codeActivation): RedirectResponse
    {
        $etat = $this->etatAbonnementRepository->findOneBy(['id' => $id]);
        $abonnement = $etat->getAbonnement();
        $entreprise = $this->entrepriseRepository->findOneBy(['user'=>$this->getUser()]);
        $nouvelEtat = new EtatAbonnement();
        $date = $definirDate->aujourdhui();
        $code = $codeActivation->generer();
        $montant = $abonnement->getPrix() + ($abonnement->getOtdSup() * (count($entreprise->getSalaries()) - $abonnement->getOtdMax()));
        $nouvelEtat->setMontant($montant)
            ->setEntreprise($entreprise)
            ->setAbonnement($abonnement)
            ->setDateDebut($date)
            ->setDatefin($etat->getDateFin())
            ->setAbonne(0)
            ->setLien($code)
            ->setCgu(1);
        $etat->setAbonne(0)
            ->setDatefin($date);
        $this->manager->persist($etat);
        $this->manager->persist($nouvelEtat);
        $this->manager->flush();

         $mail->validationAbonnnement($code, $entreprise->getUser()->getEmail(), $montant);

        return $this->redirectToRoute('entreprise');
    }

    /**
     * @Route("/validerAbo/{code}")
     * @isGranted("ROLE_ENTREPRISE")
     *
     * @param $code
     * @return RedirectResponse
     */
    public function validerAbo($code):RedirectResponse
    {
        $etat = $this->etatAbonnementRepository->findOneBy(['lien' => $code]);
        $etat->setAbonne(1)
            ->setLien(null);
        $this->manager->persist($etat);
        $this->manager->flush();
        return $this->redirectToRoute('entreprise');
    }
}
