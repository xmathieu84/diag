<?php

namespace App\Controller;

use App\Helper\SalarieRepoTrait;
use App\Service\Mail;
use App\Service\Geoloc;
use App\Service\DefinirDate;
use App\Helper\InterRepoTrait;
use App\Helper\ReservationRepoTrait;
use phpDocumentor\Reflection\Types\This;
use App\Repository\MandatCerfaRepository;
use App\Repository\MailPrefectureRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CerfaAdminController extends AbstractController
{
    use ReservationRepoTrait,SalarieRepoTrait;
    private string $cerfaInterDirectory;

    /**
     * @Route("/admin/cerfa", name="cerfa_admin")
     */
    public function index(MandatCerfaRepository $mandatCerfaRepository, Geoloc $geoloc)
    {
        $mandats = $mandatCerfaRepository->findBy(['cerfa' => null]);       
       
        return $this->render('administrateur/cerfaAdmin.html.twig', [
            'mandats' => $mandats,
        ]);
    }

    /**
     * @Route("/admin/voirCerfa/{id}",name="voirCerfa")
     *
     * @param [type] $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirCerfa($id, DefinirDate $definirDate)
    {
        $reservation = $this->reservationRepository->findOneBy(['id' => $id]);
        $date = $definirDate->aujourdhui();
        $dirigeant = $this->salarieRepository->findDirirgeant($reservation->getSalarie()->getEntreprise());
        
        return $this->render('administrateur/voirCerfa.html.twig', [
            'reservation' => $reservation,
            'date' => $date,
            'dirigeant'=>$dirigeant
        ]);
    }

    /**
     * @Route("/admin/envoieCerfa/{id}",name="AdminenvoieCerfa")
     *
     * @param [type] $id
     * @param MandatCerfaRepository $mandatCerfaRepository
     * @param Geoloc $geoloc
     * @param MailPrefectureRepository $mailPrefectureRepository
     * @param Mail $mail
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    public function envoieCerfa($id,MandatCerfaRepository $mandatCerfaRepository,Mail $mail):RedirectResponse
    {
        $mandat = $mandatCerfaRepository->findOneBy(['id'=>$id]);
        $adresseMail = $mandat->getIntervention()->getAdresse()->getDepartement()->getMailDepartement();
        $mailOTD = $mandat->getEntreprise()->getUser()->getEmail();
        $dossier = $this->getParameter('cerfaInter_directory');
        $cerfa = $mandat->getCerfa();
        $pieceJointe = $dossier.'/'.$cerfa;
        $mail->mailPJ($mailOTD,'mathieumiranda@hotmail.com',$pieceJointe);

        return $this->redirectToRoute('cerfa_admin');

    }
}
