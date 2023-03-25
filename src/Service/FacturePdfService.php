<?php

namespace App\Service;

use App\Entity\AbonnementGci;
use App\Entity\AboTotalInsti;
use App\Entity\Agent;
use App\Entity\EtatAbonnement;
use App\Entity\FactureInsti;
use App\Entity\FactureOtd;
use App\Entity\InterDiag;
use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Repository\FactureInstiRepository;
use App\Repository\FacturesRepository;
use App\Repository\PourcentageRepository;
use App\Repository\PrixOdiMissionRepository;
use App\Repository\RemiseExceptionRepository;
use DateTime;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class FacturePdfService
{
    use EntityManagerTrait,AboTotalInstiRepoTrait;

    /**
     * @var DefinirDate
     */
    protected DefinirDate $definirDate;
    /**
     * @var Environment
     */
    protected Environment $twig;
    /**
     * @var PourcentageRepository
     */
    protected PourcentageRepository $repository;
    /**
     * @var FactureInstiRepository
     */
    protected FactureInstiRepository $factureInstiRepository;
    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * @param DefinirDate $definirDate
     * @param Environment $twig
     * @param PourcentageRepository $repository
     * @param FactureInstiRepository $factureInstiRepository
     * @param Mail $mail
     */
 public function __construct(DefinirDate $definirDate,Environment $twig,
                             PourcentageRepository $repository,
                             FactureInstiRepository $factureInstiRepository,Mail $mail,
                             private FacturesRepository $facturesRepository,
                             private PrixOdiMissionRepository $prixOdiMission,
                             private PourcentageRepository $pourcentageRepository,
                             private RemiseExceptionRepository $remiseExceptionRepository)
{
    $this->definirDate = $definirDate;
    $this->twig = $twig;
    $this->repository = $repository;
    $this->factureInstiRepository = $factureInstiRepository;
    $this->mail = $mail;
    $this->facturesRepository = $facturesRepository;
    $this->prixOdiMission = $prixOdiMission;
    $this->pourcentageRepository=$pourcentageRepository;
    $this->remiseExceptionRepository=$remiseExceptionRepository;

}

    /**
     * @param EtatAbonnement $etat
     * @param $date
     * @throws MpdfException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function factureAbonnementOTD(EtatAbonnement $etat ,$date){
        $pdf = new Mpdf();
        $factures = $this->factureInstiRepository->findAll();
        $nom = $etat->getEntreprise()->getDenomination().time().'.pdf';
        $template = $this->twig->load('pdf/factureAbonnement.html.twig');
        $mois = $date->format('n');
        $jour = $date->format('d');
        $annee = $date->format('Y');
        $tva = $this->repository->findOneBy(['nom'=>'tva']);
        $otd = $etat->getAbonnement()->getOtdMax()-count($etat->getEntreprise()->getSalaries());
        $dossier = getcwd();
        $facture = new FactureOtd();
        $html = $template->render([
            'etat'=>$etat,
            'date'=>$date,
            'debut'=>DateTime::createFromFormat('d/m/Y',$jour.'/'.$mois.'/'.$annee),
            'fin'=>DateTime::createFromFormat('d/n/Y', ($jour -1) . '/' . ($mois + 1).'/'.$annee),
            'otd'=>$otd,
            'dossier'=>$dossier,
            'datePrelev'=>$date,
            'taux'=>($tva->getTaux()/100)+1,
            'tva'=>$tva->getTaux(),
            'aujour'=>$this->definirDate->aujourdhui(),
            'numero'=>$date->format("Y-m").'-'.(count($factures)+1)
        ]);

        $pdf->WriteHTML($html,0);
        $pdf->Output('../public/uploads/factureDD/'.$nom, "F");
        $facture->setEntreprise($etat->getEntreprise())
            ->setDate($this->definirDate->aujourdhui())
            ->setNom($nom)
            ->setType('Abonnement');
        $this->manager->persist($facture);
        $this->manager->flush();
    }

    /**
     * @param Agent $agent
     * @throws LoaderError
     * @throws MpdfException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function factureAbonnementGci(Agent $agent,AboTotalInsti $abonnement){
        $factures = $this->factureInstiRepository ->findAll();
        $date = $this->definirDate->aujourdhui();
        $template = $this->twig->load('pdf/factureAboInsti.html.twig');
        $nom = 1+count($factures).'GCI' . '.pdf';
        $fin = $abonnement->getFin()->sub(New \DateInterval('P1D'));

        $pdf = new Mpdf();

        $html = $template->render(
            ['abonnement' => $abonnement,
                'institution' => $agent->getDemandeur(),
                'date' => $this->definirDate->aujourdhui(),
                'numero'=>$date->format("Y-d").'-'.(count($factures)+1),
                'fin'=>$fin
            ]);
        $pdf->WriteHTML($html, 0);
        $pdf->Output('../public/uploads/factureInsti/' . $nom, 'F');
        $facture = new FactureInsti();
        $facture->setInstitution($agent->getDemandeur())
            ->setNom($nom)
            ->setDate($this->definirDate->aujourdhuiImmutable())
            ->setAbonnment(null);
        $agent->setCgv(true);
        if (!$agent->getUser()->hasRole("ROLE_ABONNE")){
            $agent->getUser()->addRole('ROLE_ABONNE');

        }
        $this->manager->persist($agent);
        $this->manager->persist($facture);

        $this->manager->flush();
        $this->mail->mailFactureAbonnementInsti($agent->getUser()->getEmail(),'../public/uploads/factureInsti/' . $nom);
    }

    public function createFactureDiag(InterDiag $inter,string $type): string
    {


        $template = $this->twig->load('pdf/factureDiag.html.twig');
        $factures = $this->facturesRepository->findAll();
        $listePrix = [];
        $tauxAcompte = $this->pourcentageRepository->findOneBy(['nom'=>"acompte"]);
        if ($inter->getPack()){
            $objetPrix = new \stdClass();
            $remisePack = $this->remiseExceptionRepository->findForPackInter(new DateTime('NOW'),$inter->getPack());
            $objetPrix->prix = $inter->getPack()->getPrix();
            $objetPrix->nom = $inter->getPack()->getPackOdi()->getPack()->getNom();
            $objetPrix->remise =  ($remisePack) ? $remisePack->getTaux() : null;
            $listePrix[]=$objetPrix;
        }
        else{
            foreach ($inter->getMissions() as $mission)
            {
                $prix = $this->prixOdiMission->findForFacture($inter->getOdi(),$mission,$inter->getTailleBien());
                $remise = $this->remiseExceptionRepository->findForInter($inter->getDateRdv(),$prix);
                $objetPrix = new \stdClass();
                $objetPrix->prix = $prix->getPrix();
                $objetPrix->nom = $prix->getMissionOdi()->getMission()->getNom();
                $objetPrix->remise =  ($remise) ? $remise->getTaux() : null;
                $listePrix[]=$objetPrix;
            }
        }


        $nom = time().'.pdf';
        try {
            $html = $template->render([
                'inter'=>$inter,
                'date'=>$this->definirDate->aujourdhui(),
                'numero'=>$this->definirDate->aujourdhui()->format('m/Y').(count($factures)+1),
                'type'=>$type,
                'liste'=>$listePrix,
                'tauxAcompte'=>$tauxAcompte->getTaux()
            ]);
        }catch (\Exception $e){
            dump($e);

        }

        $pdf = new Mpdf();
        $pdf->WriteHTML($html,0);
        $pdf->Output('../public/uploads/factureDD/'.$nom, "F");
        return $nom;
    }
}