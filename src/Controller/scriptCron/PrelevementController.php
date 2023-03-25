<?php


namespace App\Controller\scriptCron;


use App\Entity\EntreInter;
use App\Entity\FactureOtd;
use App\Repository\EntrepriseRepository;
use App\Repository\EtatAbonnementRepository;
use App\Service\DefinirDate;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Mpdf\Mpdf;
use Twig\Environment;

class PrelevementController
{

    public function factureOTD(EntrepriseRepository $entrepriseRepository,
                               EtatAbonnementRepository $etatAbonnementRepository,
                               DefinirDate $definirDate,Mail $mail,Environment $environment,EntityManagerInterface $manager){
        $entreprises = $entrepriseRepository->findAll();
        $date = $definirDate->aujourdhui()->format('m/Y');
        $dateFact = $definirDate->aujourdhui()->format('m-Y');
        $dateAbo = \DateTime::createFromFormat('d/m/Y','15/'.$date);
        foreach ($entreprises as $entreprise) {
            $etat = $etatAbonnementRepository->trouverEtatAdmin($entreprise,$dateAbo,true);


            $entreInter = new EntreInter();
            $entreInter->setAbonnement($etat);
            $entreInter->setEntreprise($entreprise);


            $pdf = new Mpdf();
            $html = $environment->render('administrateur/factureOTD.html.twig', [

                'entreInter'=>$entreInter,
                'debut'=>$definirDate->debutMoisAvant(),
                'fin'=>$definirDate->finMoisAvant()

            ]);

            $pdf->WriteHTML($html, 0);
            $nom = $entreprise->getDenomination().$dateFact.'.pdf';

            $facture = $pdf->Output('' , "S");

            $mail->mailFactureOTD($facture,$entreprise->getUser()->getEmail());
            $pdf->Output('../public/uploads/factureDD/facture'.$nom,'F');
            $facture = new FactureOtd();
            $facture->setEntreprise($entreprise)
                ->setNom($nom)
                ->setDate($definirDate->aujourdhui())
                ->getType('abonnement');
            $manager->persist($facture);
            $manager->flush();
        }





    }
}