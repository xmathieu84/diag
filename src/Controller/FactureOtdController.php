<?php

namespace App\Controller;

use App\Entity\FactureInsti;
use App\Entity\FactureOtd;
use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\etatAboRepoTrait;
use App\Repository\FactureInstiRepository;
use App\Repository\FactureOtdRepository;
use App\Repository\PourcentageRepository;
use App\Service\DefinirDate;
use DateTime;
use Mpdf\Mpdf;
use Symfony\Component\Routing\Annotation\Route;

class FactureOtdController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    use EntityManagerTrait,etatAboRepoTrait,AboTotalInstiRepoTrait;
    /**
     * @Route("/factureMensuelle/Otd/5efSH3TrKun1UFCzi8Ag1IY026eY0ERA")
     */
    public function factureMensuelle(DefinirDate $definirDate,PourcentageRepository $repository,FactureOtdRepository $factureOtdRepository){
        $etats = $this->etatAbonnementRepository->findBy(['abonne'=>true]);
        $date = $definirDate->duree($definirDate->aujourdhui(),'P14D');

        $mois = $date->format('n');
        $jour = $date->format('d');
        $annee = $date->format('Y');
        $tva = $repository->findOneBy(['nom'=>'tva']);
        foreach ($etats as $etat){

            if ($etat->getDateDebut()->format('d')===$jour){
                $pdf = new Mpdf();
                $factures = $factureOtdRepository->findAll();
                $nom = $etat->getEntreprise()->getDenomination().time().'.pdf';
                $dateprelev = $definirDate->aujourdhui();
                $mois = $date->format('n');
                $jour = $date->format('d');
                $annee = $date->format('Y');
                $tva = $repository->findOneBy(['nom'=>'tva']);
                $otd = $etat->getAbonnement()->getOtdMax()-count($etat->getEntreprise()->getSalaries());

                $facture = new FactureOtd();
                $html = $this->render('pdf/factureAbonnement.html.twig',[
                    'etat'=>$etat,
                    'date'=>$date,
                    'debut'=>DateTime::createFromFormat('d/m/Y',$jour.'/'.$mois.'/'.$annee),
                    'fin'=>DateTime::createFromFormat('d/n/Y', ($jour -1) . '/' . ($mois + 1).'/'.$annee),
                    'otd'=>$otd,
                    'taux'=>($tva->getTaux()/100)+1,
                    'tva'=>$tva->getTaux(),
                    'datePrelev'=>$dateprelev,
                    'aujour'=>$definirDate->aujourdhui(),
                    'numero'=>$annee.'-'.$mois.'-'.(count($factures)+1)
                ]);

                $pdf->WriteHTML($html,0);
                $pdf->Output('/var/www/vhosts/diag-drone.com/httpdocs/public/uploads/factureDD/'.$nom, "F");
                $facture->setEntreprise($etat->getEntreprise())
                    ->setDate($definirDate->aujourdhui())
                    ->setNom($nom)
                    ->setType('Abonnement');
                $this->manager->persist($facture);
                $this->manager->flush();
            }

        }

       exit();
    }

    /**
     * @param DefinirDate $definirDate
     * @param PourcentageRepository $repository
     * @param FactureInstiRepository $factureInstiRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Mpdf\MpdfException
     * @Route ("/factureInsti/mensuelle/5efSH3TrKun1UFCzi8Ag1IY026eY0ERA")
     */
    public function factureInstimensuelle(DefinirDate $definirDate,PourcentageRepository $repository,FactureInstiRepository $factureInstiRepository){
        $etats = $this->aboTotalInstiRepository->findBy(['abonne'=>true]);
        $date = $definirDate->duree($definirDate->aujourdhui(),'P14D');
        $mois = $date->format('n');
        $jour = $date->format('d');
        $annee = $date->format('Y');
        $tva = $repository->findOneBy(['nom'=>'tva']);

        foreach ($etats as $etat){
            $totalPack = 0;
            $fin = $etat->getFin()->sub(New \DateInterval('P1D'));
            $factures = $factureInstiRepository->findAll();
            $nom= 1+count($factures).'GCI' . '.pdf';
            $jour = $date->format('d');

            if ($etat->getDebut()->format('d') ===$jour){
                    foreach ($etat->getPackSupAboInstis() as $pack){
                        $totalPack += $pack->getPackSup()->getPrix();
                    }
                    if (!empty($etat->getPackSupAboInstis())){
                        $facturePack = new FactureInsti();
                        $pdfPack = new Mpdf();
                        $nom = count($factures).' GCI .pdf';
                        $htmlPack = $this->renderView('pdf/facturePack.html.twig',[
                            'packs'=>$etat->getPackSupAboInstis(),
                            'date'=>$date,
                            'demandeur'=>$etat->getDemandeur(),
                            'total'=>$totalPack,
                            'numero'=>$date->format("Y-m").'-'.(count($factures)+1)

                        ]);
                        $pdfPack->WriteHTML($htmlPack,0);
                        $pdfPack->Output('../public/uploads/factureInsti/'.$nom,'F');
                        $facturePack->setNom($nom)
                            ->setInstitution($etat->getDemandeur())
                        ->setDate($definirDate->aujourdhuiImmutable());
                        $this->manager->persist($facturePack);
                        $this->manager->flush();
                    }

               $pdf = new Mpdf();

                $html = $this->renderView('pdf/factureAboInsti.html.twig',
                                ['abonnement' => $etat,
                                    'institution' => $etat->getDemandeur(),
                                    'date' => $date,
                                    'numero'=>$date->format("Y-m").'-'.(count($factures)+1),
                                    'fin'=>$fin
                                ]);
                $pdf->WriteHTML($html, 0);
                $pdf->Output('/var/www/vhosts/diag-drone.com/httpdocs/public/uploads/factureInsti/' . $nom, 'F');
                $facture = new FactureInsti();
                $facture->setInstitution($etat->getDemandeur())
                    ->setNom($nom)
                    ->setDate($definirDate->aujourdhuiImmutable())
                    ->setAbonnment(null);
                $this->manager->persist($facture);
                $this->manager->flush();
            }
        }
        return $this->render('test.html.twig');

    }
}