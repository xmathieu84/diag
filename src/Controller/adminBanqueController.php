<?php


namespace App\Controller;
use App\Entity\DocBanque;
use App\Entity\EntreInter;
use App\Helper\BanqueRepoTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\DocBanqueRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\EtatAbonnementRepository;
use App\Repository\InterventionRepository;
use App\Repository\ReservationRepository;
use App\Repository\SignatureAdminRepository;
use App\Repository\UserRepository;
use App\Service\DefinirDate;
use App\Service\FactureCommisionInter;
use App\Service\MangoPayService;
use App\Service\prelevement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use Exception;
use Mpdf\Mpdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class adminBanqueController extends AbstractController
{
    use EntrepriseRepoTrait,InterRepoTrait,etatAboRepoTrait,BanqueRepoTrait,RequestTrait;

    /**
     * @param DefinirDate $definirDate
     * @param prelevement $prelevement
     * @param EntityManagerInterface $manager
     * @param DefinirDate $definirDate
     * @return Response
     * @Route("/listeVirement",name="listeVirement")
     */
    public function prelevement(DefinirDate $definirDate,prelevement $prelevement,EntityManagerInterface $manager){



        $date =$definirDate->aujourdhui();
        $mois = $date->format('n');
        $jour = $date->format('d');
        $annee = $date->format('Y');
        //$dateAbo = \DateTime::createFromFormat('d/m/Y','15/'.$date);
        setlocale(LC_TIME,['fr','fra','fr_FR']);
        $nom = 'fichierPrelevementOtd'.$definirDate->aujourdhui()->format('d-m-Y').'.xml';
        $file = fopen('../public/uploads/docBanque/'. $nom, 'wb+');

        $debut = \DateTime::createFromFormat('d/m/Y H:i:s',$jour.'/'.$mois.'/'.$annee.' 00:00:00');
        $fin = \DateTime::createFromFormat('d/m/Y H:i:s',($jour-1).'/'.($mois+1).'/'.$annee.' 23:59:59');

        $listes =[];
        $sommeTotale =0;
        $etats = $this->etatAbonnementRepository->findForPrelevement($date);

        foreach ($etats as $etat) {

            if ($jour === $etat->getDateDebut()->format('d')) {

                $totalInter = 0.00;
                $interventions = $this->interventionRepository->findByInstitution($debut, $fin, $etat->getEntreprise());

                $entreInter = new EntreInter();
                $entreInter->setEntreprise($etat->getEntreprise());
                $banque = $this->banqueRepository->findOneBy(['entreprise'=>$etat->getEntreprise(),'actif'=>true]);
                $entreInter->setAbonnement($etat);
                $entreInter->setBanque($banque);
                foreach ($interventions as $intervention) {
                    $totalInter += $intervention->getPrix();
                }
                $entreInter->setIntervention($totalInter);
                $listes[] = $entreInter;
                if ($etat->getDateDebut()->add(new \DateInterval('P4M')) > $debut) {
                    if ($etat->getAbonnement()->getNom() === 'Infinite network') {
                        $montant = $etat->getMontant() * .75 * 1.2;
                    } else {
                        $montant = 0;
                    }
                } else {
                    $montant = $etat->getMontant() * 1.2;
                }
                $entreInter->setMontantAbonnement($montant);
                $sommeTotale += $totalInter + $montant;

            }
        }
        $date = $definirDate->aujourdhui();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <CstmrDrctDbtInitn>
        <GrpHdr>
            <MsgId>PRELEVEMENT  DIAG-DRONE</MsgId>
            <CreDtTm>' . $date->format('Y-m-d') . 'T' . $date->format('H:m:s') . '</CreDtTm>
            <NbOfTxs>' . count($listes) . '</NbOfTxs>
            <CtrlSum>' . $sommeTotale . '</CtrlSum>
            <InitgPty>
                <Nm>SAS DIAG DRONE</Nm>
            </InitgPty>
        </GrpHdr>';
        foreach ($listes as $key => $liste) {

            $somme = $liste->getMontantAbonnement() + $liste->getIntervention();
            $xml .= '<PmtInf>
            <PmtInfId>REF REMISE ' . $key . '</PmtInfId>
            <PmtMtd>DD</PmtMtd>
            <NbOfTxs>1</NbOfTxs>
            <CtrlSum>' . $somme . '</CtrlSum>
            <PmtTpInf>
                <SvcLvl>
                <Cd>SEPA</Cd>
                </SvcLvl>
                <LclInstrm>
                    <Cd>CORE</Cd>
                </LclInstrm>
                <SeqTp>FRST</SeqTp>
            </PmtTpInf>
            <ReqdColltnDt>2016-09-23</ReqdColltnDt>
            <Cdtr>
                <Nm>SAS DIAG DRONE</Nm>
            </Cdtr>
            <CdtrAcct>
                <Id>
                    <IBAN>FR7616807003513660381721382</IBAN>
                </Id>
            </CdtrAcct>
            <CdtrAgt>
                <FinInstnId>
                    <BIC>CCBPFRPPGRE</BIC>
                </FinInstnId>
            </CdtrAgt>
            <ChrgBr>SLEV</ChrgBr>
            <CdtrSchmeId>
                <Id>
                    <PrvtId>
                        <Othr>
                            <Id>FR80ZZZ872CAB</Id>
                            <SchmeNm>
                                <Prtry>SEPA</Prtry>
                            </SchmeNm>
                        </Othr>
                    </PrvtId>
                </Id>
            </CdtrSchmeId>
            <DrctDbtTxInf>
                <PmtId>
                    <InstrId>131190963265894060-' . $key . '</InstrId>
                    <EndToEndId>REF1</EndToEndId>
                </PmtId>
                <InstdAmt Ccy="EUR">' . $somme . '</InstdAmt>
                <DrctDbtTx>
                    <MndtRltdInf>
                        <MndtId>RUM' . $key . '</MndtId>
                        <DtOfSgntr>' . $liste->getAbonnement()->getDateDebut()->format('Y-m-d') . '</DtOfSgntr>
                        <AmdmntInd>false</AmdmntInd>
                    </MndtRltdInf>
                </DrctDbtTx>
                <DbtrAgt>
                    <FinInstnId>
                        <BIC>' . $liste->getBanque()->getBic() . '</BIC>
                    </FinInstnId>
                </DbtrAgt>
                <Dbtr>
                    <Nm>' . $liste->getEntreprise()->getDenomination() . '</Nm>
                </Dbtr>
                <DbtrAcct>
                    <Id>
                        <IBAN>' . $liste->getBanque()->getIban() . '</IBAN>
                    </Id>
                </DbtrAcct>
                <RmtInf>
                    <Ustrd>Prélevement abonnement et commissions non percues</Ustrd>
                </RmtInf>
            </DrctDbtTxInf></PmtInf>';
        }
        $xml .= '</CstmrDrctDbtInitn></Document>';
        $adresse = ['mathieumiranda@hotmail.com'];

        fwrite($file, $xml);
        fclose($file);
        /*$doc = new DocBanque();
        $doc->setNom($nom)
            ->setDate($this->definirDate->aujourdhui());
        $this->manager->persist($doc);
        $this->manager->flush();
        $this->mail->mailBank($adresse,fopen('public/uploads/docBanque/'.$nom, 'r'),'juin');*/
        return $this->render('test.html.twig');
    }

    /**
     * @param ReservationRepository $reservationRepository
     * @throws \Mpdf\MpdfException
     * @Route ("/admin/fichierBanque",name="fichierBanque")
     */
    public function fichierBanque(DocBanqueRepository $docBanqueRepo){

        $docBanque = $docBanqueRepo->findBy([],['date'=>'DESC']);
        return $this->render('administrateur/listePrelevement.html.twig',[
            'docs'=>$docBanque
        ]);
    }

    /**
     * @param $id
     * @param DocBanqueRepository $docBanqueRepo
     * @Route ("/admin/telechargerFichierBanque/{id}",name="telechargerFichierBanque")
     */
    public function telechargerFichierBanque($id,DocBanqueRepository $docBanqueRepo){
        $docBanque = $docBanqueRepo->findOneBy(['id'=>$id]);
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header("Content-Disposition: attachment; filename='../public/uploads/docBanque/'" . $docBanque->getNom());
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize('../public/uploads/docBanque/' . $docBanque->getNom()));
        readfile('../public/uploads/docBanque/' . $docBanque->getNom());
        exit;
    }

    /**
     * @Route ("/administrateur/retrouveKyc",name="retrouveKyc")
     */
    public function retrouveKyc():Response{

        return $this->render('administrateur/retrouveKyc.html.twig');
    }

    /**
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     * @Route("/administrateur/findKyc")
     */
    public function findKyc(MangoPayService $mangoPayService):JsonResponse{
        $content = json_decode($this->request->getContent());
        $test = $mangoPayService->getKyc($content->idUser,$content->idKyc);
      
        return  new JsonResponse();
    }



}