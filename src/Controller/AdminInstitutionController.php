<?php


namespace App\Controller;


use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\FactureInstiTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminInstitutionController extends AbstractController
{
    use DemandeurRepoTrait, FactureInstiTrait, EntityManagerTrait;

    /**
     * @return Response
     * @Route("/administrateur/listeGrandCompte",name="listeGrandCompte")
     */
    public function listeGrandCompte(): Response
    {

        $institution = $this->demandeurRepository->findInstitution();

        return $this->render('administrateur/listeGrandCompte.html.twig',
            [
                'institutions' => $institution
            ]);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/administrateur/voirMandats/{id}",name="voirMandat")
     */
    public function voirMandat(int $id): Response
    {
        $institution = $this->demandeurRepository->findOneBy(['id' => $id]);
        $facture = $this->factureInstiRepository->findByMandat($institution);

        return $this->render('administrateur/voirMandat.html.twig', [
            'factures' => $facture,
            'institution' => $institution
        ]);

    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @Route ("/administrateur/validerMandat/{id}",name="validerMandat")
     */
    public function validerMandat(int $id): RedirectResponse
    {
        $facture = $this->factureInstiRepository->findOneBy(['id' => $id]);
        if ($facture->getAbonnment()) {
            $abonnment = $facture->getAbonnment();
            $abonnment->setMandatRecu(true);
            $this->manager->persist($abonnment);
        } else {
            $packs = $facture->getPackSup();
            foreach ($packs as $pack) {
                $pack->setMandatRecu(true);
                $this->manager->persist($pack);
            }
        }
        $this->manager->flush();
        return $this->redirectToRoute("voirMandat", ['id' => $facture->getInstitution()->getId()]);
    }

    /**
     * @return Response
     * @Route("/administrateur/listefacture",name="listeFactureInsti")
     */
    public function listeFactuteInsti()
    {
        $institution = $this->demandeurRepository->findInstitution();
        return $this->render('administrateur/listeFactureInsti.html.twig', [
            'institutions' => $institution
        ]);
    }
}