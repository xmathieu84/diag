<?php

namespace App\Controller;

use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\AppelOffreRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\ReponseAoRepoTrait;
use App\Repository\AnnotationRepository;
use App\Repository\BanqueRepository;
use App\Repository\DossierRepository;
use DateTime;
use Dompdf\Dompdf;
use Mpdf\Mpdf;
use App\Entity\MandatCerfa;
use App\Service\DefinirDate;
use App\Helper\InterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\MAPRepository;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\ReservationRepoTrait;
use App\Repository\DemandeurRepository;
use App\Repository\FacturesRepository;
use Mpdf\MpdfException;
use phpDocumentor\Reflection\Types\This;
use App\Repository\MandatCerfaRepository;
use App\Repository\PourcentageRepository;
use App\Repository\ReservationRepository;
use App\Repository\InterventionRepository;
use App\Repository\EtatAbonnementRepository;
use App\Repository\SignatureAdminRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TCPDF;

class PDFController extends AbstractController
{
    use EntityManagerTrait, EntrepriseRepoTrait, InterRepoTrait,etatAboRepoTrait, ReservationRepoTrait, SalarieRepoTrait,AppelOffreRepoTrait,ReponseAoRepoTrait,AgentRepoTrait,AboTotalInstiRepoTrait;

    /**
     * @Route("/pdf/proposition-{id}", name="devis_pdf")
     * @isGranted("ROLE_DEMANDEUR")
     *
     * @param integer $id
     * @param ReservationRepository $reservationRepository
     * @return PDF
     */
    public function devis($id, ReservationRepository $reservationRepository)
    {

        $reservation = $reservationRepository->findOneBy(['id' => $id]);
        $entreprise = $reservation->getSalarie()->getEntreprise();

        $pdf = new Mpdf();
        $style = file_get_contents("../public/build/app.css");
        $html = $this->renderView('pdf/proposition.html.twig', [

            'reservation' => $reservation,
            'entreprise' => $entreprise,

        ]);
        $pdf->WriteHTML($style, 1);

        $pdf->WriteHTML($html, 2);

        $pdf->Output();


        return 0;
    }

    /**
     * @Route("/pdf/facture/{id}", name="facture_pdf")
     * @isGranted("ROLE_DEMANDEUR")
     *
     * @param integer $id
     * @param ReservationRepository $reservationRepository
     * 
     */
    public function facture($id, ReservationRepository $reservationRepository)
    {

        $reservation = $reservationRepository->findOneBy(['id' => $id]);

        $pdf = new Mpdf();
        $style = file_get_contents("../public/css/css_cerfa/factureDem.css");
        $entreprise = $reservation->getSalarie()->getEntreprise();
        $reste = $reservation->getIntervention()->getPropositionChoisie()->getPrix() - $reservation->getIntervention()->getAcommpte();

        $html = $this->renderView('pdf/facture.html.twig', [
            'reservation' => $reservation,
            'entreprise' => $entreprise,
            'reste' => $reste
        ]);

        $pdf->WriteHTML($style, 1);

        $pdf->WriteHTML($html, 2);

        $pdf->Output();
        return 0;
    }

    /**
     * @Route("/salarie/facture/{id}", name="factureSalarie")
     * @isGranted("ROLE_SALARIE")
     *
     * @param integer$id
     * @param InterventionRepository $interventionRepository
     * @param PourcentageRepository $pourcentageRepository
     * @param FacturesRepository $facturesRepository
     * @return void
     */
    public function factureSalarie(
        $id,
        InterventionRepository $interventionRepository,
        PourcentageRepository $pourcentageRepository,
        FacturesRepository $facturesRepository
    ) {

        $intervention = $interventionRepository->findOneBy(['id' => $id]);
        $facture = $facturesRepository->findOneBy(['intervention' => $intervention]);


        $commission = $pourcentageRepository->findOneByNom('commission');
        $pdf = new Mpdf();
        $style = file_get_contents("../public/build/app.css");
        $html = $this->renderView('pdf/factureEntreprise.html.twig', [
            'facture' => $facture,
            'commission' => $commission
        ]);

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);

        $pdf->Output();
        return 0;
    }


    /**
     * @Route("/sepaPDF",name="sepaPDF")
     * @Security("is_granted('ROLE_ENTREPRISE') or is_granted('ROLE_MANITOU') and is_granted('ROLE_GRANDCOMPTE') or is_granted('ROLE_BTP')")
     * @param DefinirDate $definirDate
     * @return RedirectResponse
     */
    public function sepaPdf(DefinirDate $definirDate,BanqueRepository $banqueRepository): RedirectResponse
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $date = $definirDate->aujourdhui();
        if ($salarie){
            $debiteur = $salarie->getEntreprise();
            //$etat = $this->etatAbonnementRepository->trouverEtat($debiteur, $date);
            //$debut = $etat->getDateDebut();
            //$fin = $etat->getDatefin();
            $nomDebiteur = $debiteur->getDenomination();
            $formJ = $debiteur->getFormJuridique();
            $banque = $banqueRepository->findOneBy(['entreprise'=>$salarie->getEntreprise(),'actif'=>true]);
        }
        else{
            $debiteur = $agent->getDemandeur();
            //$etat = $this->aboTotalInstiRepository->findAbonnement($debiteur,$definirDate->aujourdhuiImmutable());
            $nomDebiteur = $debiteur->getNom();
            //$debut = $etat->getDebut();
            //$fin = $etat->getFin();
            $formJ = 'gc';
            $banque = $banqueRepository->findOneBy(['institution'=>$agent->getDemandeur(),'actif'=>true]);
        }
        $lieu = $debiteur->getAdresse()->getVille();


        $pdf = new Mpdf();
        $style = file_get_contents("../public/css/css_cerfa/sepa.css");
        $html = $this->renderView('pdf/sepa.html.twig', [
            'entreprise' => $debiteur,
            //'etat' => $etat,
            'date' => $date,
            //'debut'=>$debut,
            //'fin'=>$fin,
            'nom'=>$nomDebiteur,
            'formJuridique'=>$formJ,
            'lieu'=>$lieu,
            'banque'=>$banque
        ]);
        $nom = 'mandat' .$nomDebiteur . '.pdf';

        $banque->setSepa($nom);
        $this->manager->persist($debiteur);
        $this->manager->persist($banque);
        $this->manager->flush();
        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output('../public/uploads/sepa/' . $nom, "F");

        return $this->redirectToRoute('signature_electronique');
    }

    /**
     * @Route("/cguOtd/demandeur",name="cguOtdDemandeur")
     *
     * @param $id
     * @param DemandeurRepository $demandeurRepository
     * @return int
     * @throws MpdfException
     */
    public function cguPaiementAcompte( DemandeurRepository $demandeurRepository)
    {
        $demandeur = $demandeurRepository->findOneBy(['id' => $id]);
        $style = file_get_contents("../public/css/css_cerfa/cssCgu1.css");
        $html = $this->renderView('pdf/cgvdemandeurOtd.html.twig');
        $pdf = new Mpdf();

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();

        return 0;
    }

    /**
     * @Route("/cguOTDdd",name="cguOTD")
     *
     * @param EtatAbonnementRepository $etatAbo
     * @return void
     */
    public function cguDD()
    {

        $style = file_get_contents("../public/css/css_cerfa/cssCgu1.css");
        $html = $this->renderView('pdf/cguOTDdd.html.twig', []);
        $pdf = new Mpdf();

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();

        return 0;
    }

    /**
     * @Route("/formaulaireRetract",name="retract")
     *
     * @return void
     */
    public function formulaireRetractation()
    {
        $style = file_get_contents("../public/css/css_cerfa/retract.css");
        $html = $this->renderView('pdf/formulaireRetract.html.twig');
        $pdf = new Mpdf();

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();

        return 0;
    }

    /**
     * @Route("/mandatCerfaPdf/{entreprise}/{intervention}",name="cerfaPdf")
     *
     * @param int $entreprise
     * @param int $intervention
     * @param MandatCerfaRepository $mandatCerfaRepository
     * @param DefinirDate $definirdate
     * @param SignatureAdminRepository $adminSignature
     * @return RedirectResponse
     * @throws MpdfException
     */
    public function mandatCerfaPdf(int $entreprise, int $intervention, MandatCerfaRepository $mandatCerfaRepository, DefinirDate $definirdate, SignatureAdminRepository $adminSignature): RedirectResponse
    {

        $societe = $this->entrepriseRepository->findOneBy(['id' => $entreprise]);
        $inter  = $this->interventionRepository->findOneBy(['id' => $intervention]);
        $mandat = $inter->getMandatCerfa();
        $date = $definirdate->aujourdhui();
        $signature = $adminSignature->findOneBy(['nom' => 'administrateur']);
        $nom = 'mandatCerfa' . $societe->getDenomination()   .time(). '.pdf';
        if ($mandat === null) {
            $mandat = new MandatCerfa();
        }
        $mandat->setEntreprise($societe)
            ->setIntervention($inter)
            ->setDate($date)
            ->setFichierMandat($nom);
        $this->manager->persist($mandat);
        $this->manager->flush();

        $style = file_get_contents("../public/css/css_cerfa/mandatCerfa.css");
        $html = $this->renderView('pdf/mandatCerfaPdf.html.twig', [
            'entreprise' => $societe,
            'intervention' => $inter,
            'signature' => $signature
        ]);
        $pdf = new Mpdf();
        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output('../public/uploads/mandatCerfa/' . $nom, "F");

        return $this->redirectToRoute('signerMandatCerfa', [
            'intervention' => $intervention,
            'entreprise' => $entreprise
        ]);
    }
    /**
     * @Route("/envoieCerfa/{id}",name="envoieCerfa")
     *
     * @param integer $id
     * @param DefinirDate $definirDate
     * @return RedirectResponse
     */
    public function envoieCerfa($id, DefinirDate $definirDate, SignatureAdminRepository $adminSignature, MandatCerfaRepository $mandatRepo): RedirectResponse
    {
        $reservation = $this->reservationRepository->findOneBy(['id' => $id]);
        $date = $definirDate->aujourdhui();
        $signature = $adminSignature->findOneBy(['nom' => 'administrateur']);
        $dirigeant = $this->salarieRepository->findDirirgeant($reservation->getSalarie()->getEntreprise());
        $pdf = new Mpdf();
        $style = file_get_contents("../public/css/css_cerfa/style3.css");
        $html = $this->renderView('cerfa/form2.html.twig', [
            'reservation' => $reservation,
            'date' => $date,
            'signature' => $signature,
            'dirigeant'=>$dirigeant,
        ]);
        $nom = 'cerfa' . $reservation->getIntervention()->getRdvAT()->format('d-m-Y') . $reservation->getSalarie()->getCivilite()->getNom() . $reservation->getSalarie()->getCivilite()->getPrenom() . '.pdf';
        $mandat = $reservation->getIntervention()->getMandatCerfa();
        $intervention = $reservation->getIntervention();


        $mandat->setCerfa($nom);
        $this->manager->persist($mandat);
        $this->manager->flush();

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output('../public/uploads/cerfaInter/' . $nom, "F");
        return $this->redirectToRoute('AdminenvoieCerfa', [
            'id' => $reservation->getIntervention()->getMandatCerfa()->getId()
        ]);
    }

    /**
     * @Route("/MAP/{id}",name="mapPdf")
     *
     * @param [type] $id
     * @param [type] $definirDate
     * @param MAPRepository $mapRepository
     * @return void
     */
    public function exportMap($id, DefinirDate $definirDate, MAPRepository $mapRepository)
    {
        $salarie = $this->salarieRepository->findOneBy(['id' => $id]);
        $debut = $definirDate->DebutAnnee();
        $fin = $definirDate->FinDannee();
        $maps = $mapRepository->findMapBySalarie($salarie->getId(), $debut, $fin);
        $debutTemps = new DateTime();
        $debutTemps->setTimestamp(0);
        $finTemps = new DateTime();
        $finTemps->setTimestamp(0);
        foreach ($maps as $map) {
            $duree = $finTemps->add($map->getDureeVol());
        }
        $tempsFormatte = date_diff($duree, $debutTemps);
        $total = 24 * $tempsFormatte->format('%d') + $tempsFormatte->format('%h') . ' h ' . $tempsFormatte->format('%i');
        $maps = $mapRepository->findMapBySalarie($salarie->getId(), $debut, $fin);
        $html  = $this->renderView('pdf/MAP.html.twig', [
            'maps' => $maps,
            'salarie' => $salarie,
            'total' => $total,
            'annee' => $debut->format('Y')
        ]);
        $style = file_get_contents('../public/css/css_cerfa/cssMAP.css');
        $pdf = new Mpdf(['orientation' => 'L']);

        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
        return 0;

    }

    /**
     * @Route("/factureInstitutionnel/{id}",name="factureInstitutionnel")
     *
     * @param [type] $id
     * @param ReservationRepository $reservationRepository
     * @return void
     */
    public function factureInstitutionnel($id, ReservationRepository $reservationRepository)
    {
        $reservation = $reservationRepository->findOneBy(['id' => $id]);

        $pdf = new Mpdf();
        $style = file_get_contents("../public/css/css_cerfa/factureDem.css");
        $entreprise = $reservation->getSalarie()->getEntreprise();
        $reste = $reservation->getIntervention()->getPrix();

        $html = $this->renderView('pdf/factureInsituttionnel.html.twig', [
            'reservation' => $reservation,
            'entreprise' => $entreprise,
            'reste' => $reste
        ]);

        $pdf->WriteHTML($style, 1);

        $pdf->WriteHTML($html, 2);

        $pdf->Output();
        return 0;
    }

    /**
     * @Route("/formF1")
     *
     * @return void
     */
    public function formF1()
    {
        $pdf = new Mpdf([
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => 10
        ]);
        $style = file_get_contents("../public/css/css_cerfa/formf1.css");
        $header = $this->renderView('pdf/headerForm.html.twig');
        $html = $this->renderView('pdf/formF1.html.twig');
        $pdf->SetHTMLHeader($header);
        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);

        $pdf->Output();
        return 0;
    }
    /**
     * @Route("/formF3")
     *
     * @return void
     */
    public function formF3()
    {
        $pdf = new Mpdf([
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => 20
        ]);
        $style = file_get_contents("../public/css/css_cerfa/formf3.css");
        $header = $this->renderView('pdf/headerFormF3.html.twig');
        $html = $this->renderView('pdf/formF3.html.twig');
        $pdf->SetHTMLHeader($header);
        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);

        $pdf->Output();
        return 0;
    }

    /**
     * @Route("/formF3bis")
     *
     * @return void
     */
    public function formF3bis()
    {
        $pdf = new Mpdf([
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => 20
        ]);
        $style = file_get_contents("../public/css/css_cerfa/formf3.css");
        $header = $this->renderView('pdf/headerFormF3.html.twig');
        $html = $this->renderView('pdf/formF3bis.html.twig');
        $pdf->SetHTMLHeader($header);
        $pdf->WriteHTML($style, 1);
        $pdf->WriteHTML($html, 2);

        $pdf->Output();
        exit;
    }

    /**
     * @param int $id
     * @throws MpdfException
     * @Route("/institution/ficheAO/{id}",name="ficheAO")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1') or is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1GC') or is_granted('ROLE_PREMIUM')")
     */
    public function ficheAO(int $id){
        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);
        $pdf = new Mpdf();
        $html = $this->renderView('pdf/fichierAO.html.twig',[
            'appel'=>$appel
        ]);
        $pdf->WriteHTML($html,0);
        $pdf->Output();
        exit;
    }

    /**
     * @param int $id
     * @throws MpdfException
     * @Route("/ficheReponse-{id}", name="ficheReponse")
     */
    public function ficherReponseAo(int $id){
        $reponse = $this->reponseAoRepository->findOneBy(['id'=>$id]);
        $pdf = new Mpdf();
        $html = $this->renderView('pdf/ficherReponseAo.html.twig',['reponse'=>$reponse]);
        $pdf->WriteHTML($html,0);
        $pdf->Output();
        exit;
    }

    /**
     * @param int $id
     * @param DossierRepository $repository
     * @Route("/dossierPdf/{id}",name="dossierPdf")
     * @throws MpdfException
     */
    public function dossierPdf(int $id,DossierRepository $repository,AnnotationRepository $repo){
        $dossier = $repository->findOneBy(['id'=>$id]);
        $notes = $repo->findByDossier($dossier);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $dossierRange=[];
        foreach ($dossier->getSousDossiers() as $sousDossier){
            foreach ($sousDossier->getDocSousDossiers() as $docSousDossier){
                $dossierRange[] = [
                    'type' => $docSousDossier->getSousDossier()->getType(),
                    'date'=>$docSousDossier->getDate()->format('d-m-Y'),
                    'nom'=>$docSousDossier->getLibelle(),
                    'alerte'=>$docSousDossier->getDelaiAlerte(),
                    'piece'=>$docSousDossier,
                    'dateA'=>$docSousDossier->getDate()->add($docSousDossier->getDelaiAlerte())
                ];
            }
        }
        foreach ($dossier->getDossierGeneral()->getPiecesgenerales() as $piecesgenerale){
            $dossierRange[] = [
                'type' => 'Pièce générale',
                'date'=>$piecesgenerale->getDate()->format('d-m-Y'),
                'nom'=>$piecesgenerale->getNom(),
                'alerte'=>null,
                'piece'=>$piecesgenerale,
                'dateA'=>null

            ];
        }
        array_multisort(array_column($dossierRange,'date'),SORT_DESC,$dossierRange);
        $pdf = new Mpdf();
        $style = file_get_contents('../public/css/css_cerfa/dossier.css');
        $headerPage1 = $this->renderView('pdf/headerFooter/header.html.twig',[
            'dossier'=>$dossier,
            'insitution'=>$agent->getDemandeur()
        ]);
        $pdf->DefHTMLHeaderByName('headerPage1',$headerPage1);
        $html = $this->renderView('pdf/page1Dossier.html.twig',[
            'dossier'=>$dossier,
            'Rdossiers'=>$dossierRange,
            'insitution'=>$agent->getDemandeur()
        ]);
        $html2 = $this->renderView('pdf/page2Dossier.html.twig',[
            'notes'=>$notes
        ]);
        $html3 = $this->renderView('pdf/page3Dossier.html.twig',['dossierRange'=>$dossierRange]);
        $pdf->AddPage('','','','','','2','2','2','3','1','', '', '', '', '', -1, -1, -1, -1);
        $pdf->WriteHTML($style,1);
        $pdf->WriteHTML($html,2);

        $header = $this->renderView('pdf/headerFooter/headerDossier.html.twig',['dossier'=>$dossier]);

        $pdf->DefHTMLHeaderByName('header1',$header);

        $pdf->AddPage( '','','','','','','','70','','','',
            'header1', '', '', '', 1, 0, 1, 0
        );
        $pdf->WriteHTML($html2,2);
        $header2 = $this->renderView('pdf/headerFooter/header2.html.twig',['dossier'=>$dossier]);
        $pdf->DefHTMLHeaderByName('header2',$header2);
        $pdf->AddPage( '','','','','','','','70','','','',
            'header2', '', '', '', 1, 0, 1, 0);

        $pdf->WriteHTML($html3,2);



       $pdf->Output();


        
        exit();

        //return $this->render('test.html.twig',['Rdossiers'=>$dossierRange]);
    }
}
