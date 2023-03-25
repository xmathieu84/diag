<?php

namespace App\EventSubscriber;

use App\Entity\PrixOdiMission;
use App\Event\FactureDiagEvent;
use App\Repository\FacturesRepository;
use App\Repository\PourcentageRepository;
use App\Repository\PrixOdiMissionRepository;
use App\Repository\RemiseExceptionRepository;
use App\Service\DefinirDate;
use JetBrains\PhpStorm\ArrayShape;
use Mpdf\Mpdf;
use Symfony\Component\EventDispatcher\GenericEvent;
use Twig\Environment;

class FactureDiagSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public function __construct(private Environment $twig,private DefinirDate $definirDate,
                                private FacturesRepository $facturesRepository,
                                private PrixOdiMissionRepository $prixOdiMission,
                                private RemiseExceptionRepository $remiseExceptionRepository,
                                private PourcentageRepository $pourcentageRepository)
    {
        $this->twig=$twig;
        $this->definirDate=$definirDate;
        $this->facturesRepository = $facturesRepository;
        $this->prixOdiMission = $prixOdiMission;
        $this->remiseExceptionRepository=$remiseExceptionRepository;
        $this->pourcentageRepository=$pourcentageRepository;
    }

    /**
     * @inheritDoc
     */
   public static function getSubscribedEvents():array
    {
        return [
            FactureDiagEvent::NAME=>"createFactureDiag"
        ];
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \Mpdf\MpdfException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createFactureDiag(GenericEvent $event): string
    {

        $inter = $event->getSubject();
        $template = $this->twig->load('pdf/factureDiag.html.twig');
        $factures = $this->facturesRepository->findAll();
        $listePrix = [];
        $tauxAcompte = $this->pourcentageRepository->findOneBy(['nom'=>"acompte"]);
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

        $nom = time().'.pdf';
        try {
            $html = $template->render([
                'inter'=>$inter,
                'date'=>$this->definirDate->aujourdhui(),
                'numero'=>$this->definirDate->aujourdhui()->format('m/Y').(count($factures)+1),
                'type'=>$event->getArgument('type'),
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