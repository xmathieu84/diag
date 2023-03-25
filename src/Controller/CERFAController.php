<?php

namespace App\Controller;


use Exception;

use Mpdf\Mpdf;

use App\Service\DefinirDate;
use App\Helper\InterRepoTrait;

use App\Service\choixTemplate;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\ReservationRepoTrait;
use App\Repository\MandatCerfaRepository;
use App\Repository\SignatureAdminRepository;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class CERFAController
 * @package App\Controller
 */
class CERFAController extends AbstractController
{

    use ReservationRepoTrait, EntrepriseRepoTrait, InterRepoTrait, etatAboRepoTrait;



    /**
     * @param $id
     * @param DefinirDate $definirDate
     * @return void
     * @throws MpdfException
     * @Route("/cerfa/{id}", name="cerfa")
     * @isGranted("ROLE_SALARIE")
     */
    public function cerfa(
        $id,
        DefinirDate $definirDate
    ):void {
        $reservation = $this->reservationRepository->findOneById($id);
        $date =  $definirDate->aujourdhui();
        $pdf = new Mpdf();
        $style1 = file_get_contents("../public/css/css_cerfa/style3.css");
        $html = $this->renderView('cerfa/form2.html.twig', [
            'reservation' => $reservation,
            'date' => $date
        ]);
        $dossier = $this->getParameter('cerfa_directory');
        $pdf->WriteHTML($style1, 1);

        $pdf->WriteHTML($html, 2);
        $pdf->Output();
        return ;
    }


    /**
     * @param $entreprise
     * @param $intervention
     * @param SignatureAdminRepository $adminSignature
     * @return Response
     * @Route("/mandatCerfa/{entreprise}/{intervention}",name="mandatCerfa")
     * @isGranted("ROLE_SALARIE")
     */
    public function mandatCerfa($entreprise, $intervention, SignatureAdminRepository $adminSignature):Response
    {

        $societe = $this->entrepriseRepository->findOneBy(['id' => $entreprise]);
        $inter  = $this->interventionRepository->findOneBy(['id' => $intervention]);

        $signature = $adminSignature->findOneBy(['nom' => 'administrateur']);
        return $this->render('cerfa/mandat.html.twig', [
            'entreprise' => $societe,
            'intervention' => $inter,
            'signature' => $signature
        ]);
    }
}
