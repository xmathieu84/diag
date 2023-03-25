<?php

namespace App\Controller;

use App\Entity\DevisAdmin;
use App\Entity\FactureAdmin;
use App\Helper\EntityManagerTrait;
use App\Repository\DevisAdminRepository;
use App\Repository\FactureAdminRepository;
use App\Service\DefinirDate;
use DateTime;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FactAdminController extends AbstractController
{
    use EntityManagerTrait;

    /**
     * @Route("/admin/fact/admin", name="factureAdmin")
     */
    public function index()
    {
        return $this->render('fact_admin/index.html.twig', []);
    }

    /**
     * @Route("/admin/doc/pdf")
     *
     * @param Request $request
     * @param DefinirDate $definirDate
     * @return JsonResponse
     */
    public function factureAdminPdf(Request $request, DefinirDate $definirDate): JsonResponse
    {


        $contenu = json_decode($request->getContent(), true);
        $pdf = new Mpdf();
        $style = file_get_contents("../public/css/css_cerfa/facturePdf.css");
        $content = [
            'entreprise' => $contenu['entreprise'],
            'lignes' => $contenu['objet'],
            'numero' => $contenu['numeroFact'],
            'date' => $contenu['dateFact'],
            'tva' => floatval($contenu['TVA']),
            'HT' => floatval($contenu['HT']),
            'TTC' => floatval($contenu['TTC'])
        ];
        $date = $definirDate->aujourdhui();
        if ($contenu['type'] == 'devis') {
            $html = $this->renderView('pdf/devisAdmin.html.twig', $content);
            $dossier = $this->getParameter('devisAdmin_directory');
            $sortie = $dossier . '/devis' . $contenu['numeroFact'] . '.pdf';
            $document = new DevisAdmin();
            $nom = 'devis' . $contenu['numeroFact'];
            $document->setDate($date)
                ->setNom($nom);
        } else {
            $html = $this->renderView('pdf/factureAdmin.html.twig', $content);
            $dossier = $this->getParameter('factureAdmin_directory');
            $sortie = $dossier . '/facture' . $contenu['numeroFact'] . '.pdf';
            $nom = 'facture' . $contenu['numeroFact'];
            $document = new FactureAdmin();
            $document->setDate($date)
                ->setNom($nom);
        }
        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);

        $pdf->Output($sortie, "F");

        $this->manager->persist($document);
        $this->manager->flush();
        $reponse = new JsonResponse();

        return $reponse->setData($nom);
    }

    /**
     * @Route("/admin/listeFacture",name="listeFacture")
     *
     * @return void
     */
    public function ListeFacture()
    {
        return $this->render('fact_admin/listeFacture.html.twig');
    }

    /**
     * @Route("/admin/retourListe")
     *
     * @param Request $request
     * @param FactureAdminRepository $factureAdminRepository
     * @return JsonResponse
     */
    public function retourListeFacture(Request $request, FactureAdminRepository $factureAdminRepository)
    {
        $contenu = json_decode($request->getContent(), true);
        $debut = DateTime::createFromFormat('!d/m/Y', $contenu['debut']);
        $fin = DateTime::createFromFormat('d/m/Y H:i:s', $contenu['fin'] . '23:59:59');
        $listeFactures = [];
        $factures  = $factureAdminRepository->findByDateFacture($debut, $fin);
        foreach ($factures as $facture) {
            array_push($listeFactures, $facture->getNom());
        }


        $reponse = new JsonResponse();

        return $reponse->setData($listeFactures);
    }

    /**
     * @Route("/admin/listeDevis")
     *
     * @param Request $request
     * @param DevisAdminRepository $devisAdminRepository
     * @return JsonResponse
     */
    public function retourListeDevis(Request $request, DevisAdminRepository $devisAdminRepository): JsonResponse
    {
        $contenu = json_decode($request->getContent(), true);
        $debut = DateTime::createFromFormat('!d/m/Y', $contenu['debut']);
        $fin = DateTime::createFromFormat('d/m/Y H:i:s', $contenu['fin'] . '23:59:59');
        $listeDevis = [];
        $factures  = $devisAdminRepository->findByDateDevis($debut, $fin);
        foreach ($factures as $facture) {
            array_push($listeDevis, $facture->getNom());
        }


        $reponse = new JsonResponse();

        return $reponse->setData($listeDevis);
    }
}
