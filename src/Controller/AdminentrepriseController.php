<?php

namespace App\Controller;

use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\PaginatorTrait;
use App\Helper\RequestTrait;
use App\Helper\ReservationRepoTrait;
use App\Helper\SalarieRepoTrait;
use DateTime;
use DateInterval;
use Mpdf\Mpdf;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\SalarieRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\EtatAbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use App\Repository\IndisponibiliteRepository;
use App\Service\DefinirDate;
use App\Service\Mail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminentrepriseController extends AbstractController
{
    use EntrepriseRepoTrait, SalarieRepoTrait, EntityManagerTrait, ReservationRepoTrait, PaginatorTrait, RequestTrait;
    /**
     * @Route("/administrateur/entreprise", name="listeEntreprise")
     */
    public function listeEnt(DefinirDate $definirDate)
    {

        $entreprises = $this->entrepriseRepository->findBy(['cgv' => true]);
        $date = $definirDate->aujourdhui();

        return $this->render('administrateur/entreprise.html.twig', [
            'entreprises' => $entreprises,
            'date' => $date
        ]);
    }
    /**
     * @Route("/administrateur/listeSalarie/{entreprise}",name="listeSalarie")
     *
     *
     */
    public function listeSalarie($entreprise)

    {
        $boite = $this->entrepriseRepository->findOneBy(['id' => $entreprise]);
        $salaries = $this->salarieRepository->findBy(['entreprise' => $entreprise]);

        return $this->render('administrateur/listeSalarie.html.twig', [
            'salaries' => $salaries,
            'entreprise' => $boite
        ]);
    }
    /**
     * @Route("/telechargerLicence/{licence}",name="telechargerLicence")
     *
     * 
     */
    public function licenceSalarie($licence)

    {
        $dossier = "../public/uploads/licence/";
        $reponse = new Response();
        $reponse->setContent(file_get_contents($dossier . $licence));
        $reponse->headers->set('Content-Type', 'application/force-download');
        $reponse->headers->set('Content-disposition', 'filename=' . $licence);
        return $reponse;
    }
    /**
     * @Route("/administrateur/validation/{id}",name="validation")
     *
     * 
     * 
     * @return void
     */
    public function validationSalarie($id)
    {
        $salarie = $this->salarieRepository->findOneBy(['id' => $id]);
        $salarie->setValidation('valide');
        $entreprise = $salarie->getEntreprise()->getId();
        $this->manager->persist($salarie);
        $this->manager->flush();
        return $this->redirectToRoute('listeSalarie', ['entreprise' => $entreprise]);
    }
    /**
     * @Route("/administrateur/suspendre/{id}",name="suspendre")
     *
     * 
     * 
     * @return void
     */
    public function suspendreSalarie($id)
    {
        $salarie = $this->salarieRepository->findOneBy(['id' => $id]);
        $salarie->setValidation('en cours');
        $entreprise = $salarie->getEntreprise()->getId();
        $this->manager->persist($salarie);
        $this->manager->flush();
        return $this->redirectToRoute('listeSalarie', ['entreprise' => $entreprise]);
    }
    /**
     * @Route("/administrateur/entreprise/intervention/{id}",name="entrepriseInter")
     */

    public function interventions($id)
    {

        $entreprise = $this->entrepriseRepository->findOneBy(['id' => $id]);
        $reservations = [];
        $salaries = $this->salarieRepository->findBy(['entreprise' => $entreprise]);
        foreach ($salaries as $salarie) {
            $reservation = $this->reservationRepository->findOneBy(['salarie' => $salarie]);
            array_push($reservations, $reservation);
        }

        return $this->render('administrateur/entreInter.html.twig', [
            'entreprise' => $entreprise,
            'reservations' => $reservations

        ]);
    }


    /**
     * @Route("/administrateur/listeOTD",name="listeOTD")
     */
    public function carte()
    {
        return $this->render('administrateur/listOTD.html.twig');
    }
    /**
     * @Route("/administrateur/marker")
     */

    public function marker()
    {
        $salaries = $this->salarieRepository->findAll();

        $coordonees = [];
        $latitude = [];
        $longitude = [];

        foreach ($salaries as $key => $salarie) {
            $latitude['lat'] = $salarie->getAdresse()->getCoordonnees()->getLatitude();
            $longitude['lng'] = $salarie->getAdresse()->getCoordonnees()->getLongitude();

            $coordonees[$key] = [$latitude['lat'], $longitude['lng']];
        }

        $reponse = new JsonResponse();
        $reponse->setData($coordonees);

        return $reponse;
    }

    /**
     * Undocumented function
     * @Route("/administrateur/envoieMail")
     * @param [type] $id
     * @return void
     */
    public function envoieMail(
        UserRepository $userRepository,
        MailerInterface $mailer,
        EntityManagerInterface $manager,
        Request $request
    ) {
        $id = $request->getContent();
        $user = $userRepository->findOneBy(['id' => $id]);
        $adresseMail = $user->getEmail();
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $user->getUserEnt()->setMailrappel($date);
        $manager->persist($user);
        $manager->flush();
        $email = (new Email())
            ->from('contact@diag-drone.com')
            ->to($adresseMail)
            ->priority(Email::PRIORITY_HIGH)
            ->subject("Fin d'abonnement")
            ->text("Votre abonnement expire bientôt.Pensez à le renouveler");
        $mailer->send($email);

        $reponse = new JsonResponse();

        return $reponse;
    }

    /**
     * @Route("/administrateur/listeAbo",name="listeAbo")
     *
     * @param EntrepriseRepository $entrepriseRepository
     * @return void
     */
    public function ListeAbo(EtatAbonnementRepository $eaRepository, DefinirDate $definirDate): Response
    {
        $date = $definirDate->aujourdhui();
        $etats = $this->paginator->paginate($eaRepository->findByEntreprise($date), $this->request->query->getInt('page', 1), 1);

        return $this->render('administrateur/listeabo.html.twig', [
            'etats' => $etats,
            'date' => $date
        ]);
    }

    /**
     * @Route("/administrateur/valideAbonnement/{id}",name="AdminValideAbo")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Mail $mail
     * @param EntrepriseRepository $entrepriseRepository
     * @return void
     */
    public function valideAbonnment(EntityManagerInterface $manager, Mail $mail, $id, EtatAbonnementRepository $eaRepository)
    {

        $etat = $eaRepository->findOneBy(['id' => $id]);
        $nom = $etat->getAbonnement()->getNom();
        foreach ($etat->getEntreprise()->getSalaries() as $salary){
            switch ($nom){
                case 'So free';
                $role = 'ROLE_FREE';
                break;
                case 'Classic access';
                    $role = 'ROLE_CLASIC';
                    break;
                case 'Premium network';
                    $role = 'ROLE_PREMIUM';
                    break;
                case 'Infinite network';
                    $role = 'ROLE_INFINITE';
                    break;
            }
            $salary->getUser()->addRole($role);
            $manager->persist($salary);
        }
        $etat->setAbonne(true);
        $manager->persist($etat);
        $manager->flush();
        $contenu = "<p>Bonjour.</p><p>Votre abonnement est validé.Vous pouvez maintenant profiter des services de Diag-drone!</p>";
       // $envoieMail = $mail->mailInter([$etat->getEntreprise()->getUser()->getEmail()], $contenu);
        return $this->redirectToRoute('listeAbo');
    }

    /**
     * @Route("/administrateur/suspendreAbonnement/{id}",name="AdminSuspendreAbo")
     *
     * @param [type] $id
     * @param EtatAbonnementRepository $eaRepository
     * @return void
     */
    public function suspendreAbonnement($id, EtatAbonnementRepository $eaRepository)
    {
        $etat  = $eaRepository->findOneBy(['id' => $id]);
        $etat->setAbonne(false);
        $this->manager->persist($etat);
        $this->manager->flush($etat);

        return $this->redirectToRoute('listeAbo');
    }


    /**
     * @return Response
     * @Route ("/administrateur/infoMangoPay",name="infoMangoPay")
     */
    public function infoMangoPay(){
        $dirigeant = $this->salarieRepository->findAllDirirgeant();

        return $this->render('administrateur/infoMangoPay.html.twig',[
            'dirigeants'=>$dirigeant
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/rechercheDirigeant")
     */
    public function rechercheDirigeant():JsonResponse{
        $nom = $this->request->getContent();
        $salaries = $this->salarieRepository->findDirigeantByMail($nom);
        $liste=[];
        foreach ($salaries as $salary){
            $liste[]=[
                'nom'=>$salary->getCivilite()->getNom(),
                'prenom'=>$salary->getCivilite()->getPrenom(),
                'entreprise'=>$salary->getEntreprise()->getDenomination(),
                'email'=>$salary->getUser()->getEmail(),
                'mangoUser'=>$salary->getUser()->getMangoPayId(),
                'wallet'=>$salary->getUser()->getWalletMangoid(),
                'bank'=>$salary->getUser()->getBankMangoPay(),
                'ubo'=>$salary->getEntreprise()->getUboDeclaration()->getIdUbo(),
                'resultat'=>$salary->getEntreprise()->getUboDeclaration()->getResultat()
                ];
        }

        return new JsonResponse($liste);

    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/administrateur/modiferCom/{id}")
     */
    public function modifCom($id):JsonResponse{
        $entreprise = $this->entrepriseRepository->findOneBy(['id'=>$id]);
        $entreprise->setCommission($this->request->getContent());
        $this->manager->persist($entreprise);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @throws \Mpdf\MpdfException
     * @Route ("/administrateur/exportOtd",name="exportOtd")
     */
    public function exportOtd(){
        $dirigeants = $this->salarieRepository->findAllDirirgeant();
        $pdf = new Mpdf([
            'orientation'=>'L'
        ]);
        $html = $this->renderView('pdf/exportOd.html.twig',['dirigeant'=>$dirigeants]);
        $pdf->WriteHTML($html,0);
        $pdf->Output();
        exit;
    }
}
