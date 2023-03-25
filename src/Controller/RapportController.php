<?php

namespace App\Controller;


use App\Entity\Incident;
use App\Helper\AgentRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\FactureCommisionInter;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\Mime\Address;
use ZipArchive;
use App\Entity\Photo;
use App\Service\Mail;
use App\Entity\Rapport;
use App\Entity\Factures;
use App\Service\Fichier;
use App\Form\RapportType;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use App\Helper\InterRepoTrait;
use App\Helper\PhotoRepoTrait;
use App\Service\codeActivation;
use App\Helper\RapportRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\ReservationRepoTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class RapportController extends AbstractController
{
    use InterRepoTrait, EntityManagerTrait, RequestTrait, PhotoRepoTrait, RapportRepoTrait, ReservationRepoTrait,SalarieRepoTrait,AgentRepoTrait;
    private string $photosDirectory;
    private string $fichierDirectory;
    private string $cerfaInterDirectory;
    private string $videoDirectory;

    /**
     * @Route("/rapport/{id}", name="rapport")
     * @isGranted("ROLE_SALARIE")
     */
    public function index($id, DefinirDate $definirDate, Fichier $fichier, Mail $mail)
    {
        $date = $definirDate->aujourdhui();
        $intervention = $this->interventionRepository->findOneBy(['id' => $id]);
        $reservation = $this->reservationRepository->findOneBy(['intervention' => $intervention]);

        $adresseMailDemandeur = [];
        $salarie = $reservation->getSalarie();
        if ($intervention->getIntDem()->getUser()) {
            $adresseMailDemandeur = [$intervention->getIntDem()->getUser()->getEmail()];
        } else {
            foreach ($intervention->getIntDem()->getAgents() as $agent) {
                $adresseMailDemandeur[] = new Address($agent->getUser()->getEmail());
            }
            $intervention->setDatePaiement($date);
            $this->manager->persist($intervention);
            $this->manager->flush();
        }
        $rapport = $this->rapportRepository->findOneBy(['intervention'=>$intervention,'statu_rapport'=>'Ecrit']);
        if (!$rapport) {
            $rapport = new Rapport();
            $rapport->setIntervention($intervention)
            ->setStatuRapport('Ecrit');
            $this->manager->persist($rapport);
            $this->manager->flush();
            return $this->redirectToRoute('rapport',['id'=>$id]);
        }

        $foto = $this->photoRepository->findBy(['rapport' => $rapport]);

        $form = $this->createForm(RapportType::class, $rapport);

        $form->handleRequest($this->request);

        if ($form->isSubmitted()) {
            if ($form['degatInter']->getData()==='oui'){
                $incident = new Incident();
                $incident->setDegatCorporel($form['degat']['degatCorporel']->getData())
                    ->setDegatMateriel($form['degat']['degatMateriel']->getData())
                    ->setIntervention($intervention);
                $this->manager->persist($incident);
            }
            $photos = $form->get('photos')->getData();

            $reponse = $form['confirmation']->getData();
            if ($intervention->getIntDem()->getUser()) {
                $date2 = $form['date']->getData();
                if ($date2 !== null) {
                    $intervention->setDatePaiement($date2);
                    $this->manager->persist($intervention);
                }
            }

            $rapport->setRapResume(explode(',', $form['rap_resume']->getData()));
            if ($rapport->getIntervention()->getIntDem()->getCivilite()){
                $nom = $intervention->getIntDem()->getCivilite()->getNom();
            }else{
                $nom = $intervention->getIntDem()->getProfil().'-'.$intervention->getIntDem()->getNom();
            }
            if (count($photos)>0){
                foreach ($photos as $key=> $photo) {
                    $nouveauNom = $fichier->moveFile($photo, $this->getParameter('photos_directory'),$nom.$key);
                    $foto  = new Photo();
                    $foto->setNom($nouveauNom);
                    $rapport->addPhoto($foto);

                }
            }

            if ($form->get('donnees_telemetrique')->getData()) {
                $fichierTelemetrique = $fichier->moveFile('telemetrie',$form->get('donnees_telemetrique')->getData(), $this->getParameter('fichier_directory'));
                $rapport->setDonneesTelemetrique($fichierTelemetrique);
            }
            if ($form->get('rap_fichier')->getData()) {
                $fichierAutre = $fichier->moveFile('autre',$form->get('rap_fichier')->getData(), $this->getParameter('fichier_directory'));
                $rapport->setRapFichier($fichierAutre);
            }
            if ($form->get('cerfa_inter')->getData()) {
                $fichierCerfa = $fichier->moveFile('cerfa',$form->get('cerfa_inter')->getData(), $this->getParameter('fichier_directory'));
                $rapport->setCerfaInter($fichierCerfa);
            }


            if ($reponse === "non") {
                $rapport->setStatuRapport('Ecrit')
                    ->setIntervention($intervention);

                $this->manager->persist($rapport);
                $this->manager->flush();
                return $this->redirectToRoute('mesinter');
            } elseif ($reponse === 'oui') {



                $rapport->setStatuRapport('termine')
                    ->setIntervention($intervention);
                $intervention->setStatuInter('termine');

              $this->manager->persist($intervention);
                $this->manager->persist($rapport);

                $this->manager->flush();
                if (!empty($adresseMailDemandeur)) {
                    $mail->mailRapport($adresseMailDemandeur);
                }


                return $this->redirectToRoute('creationRapport', [
                    'id' => $rapport->getId()
                ]);
            }
        }
        return $this->render('rapport/index.html.twig', [
            'form' => $form->createView(),
            'photos' => $foto,
            'intervention' => $intervention,
            'rapport' => $rapport
        ]);
    }


    /**
     * @Route("/creation/rapport-{id}/zip",name="creationRapport")
     *
     * @param int $id
     * @return Response
     */
    public function attenteCreationZip(int $id,DefinirDate $definirDate,Fichier $fichier): Response
    {

        $rapport = $this->rapportRepository->findOneBy(['id'=>$id]);
        if ($rapport->getIntervention()->getTypeInter()->getNom() !=="Captation audiovisuelle") {
            $pdf = new Mpdf();
            $coordInter = $rapport->getIntervention()->getAdresse()->getCoordonnees();

            $marker = "geojson(%7B%22type%22%3A%22Point%22%2C%22coordinates%22%3A%5B{$coordInter->getLongitude()}%2C{$coordInter->getLatitude()}%5D%7D)";
            $mapBoxAccessToken = 'pk.eyJ1IjoieG1hdGhpZXUxMyIsImEiOiJja2N1aHU2eTAyOGZ2MnJsZjk1bjR0ZHE0In0.-N1CSInz77-O6xViA43KQw';
            $coordInterImgURL = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/{$marker}/{$coordInter->getLongitude()},{$coordInter->getLatitude()},8.5/800x600?access_token={$mapBoxAccessToken}";

            foreach ($rapport->getPhotos() as $photo) {
                $fichier->convertImage('../public/uploads/photoInter/' . $photo->getNom(), '../public/uploads/photoRapport/' . $photo->getNom(), 480, 280, 100);
            }
            $dirigeant = $this->salarieRepository->findDirirgeant($rapport->getIntervention()->getReservation()->getSalarie()->getEntreprise());
            $responsable = $this->agentRepository->findResponsable($rapport->getIntervention()->getIntDem());

            $html = $this->renderView('pdf/rapport.html.twig', ['rapport' => $rapport]);
            dump($rapport->getIntervention()->getReservation()->getSalarie()->getEntreprise()->getEntAss());
            $html2 = $this->renderView('pdf/rapport2.html.twig', [
                'rapport' => $rapport,
                'responsable' => $responsable,
                'dirigeant' => $dirigeant,
                'date' => $definirDate->aujourdhui(),
                'coordInterImgURL' => $coordInterImgURL
            ]);

            $style = file_get_contents("../public/css/css_cerfa/rapport.css");
            $nom = "1-rapport d'intervention n°" . time() . ".pdf";
            $pdf->WriteHTML($style, 1);
            $pdf->WriteHTML($html, 2);
            $pdf->WriteHTML($html2, 2);
            $pdf->Output('../public/uploads/rapport/' . $nom, 'F');
            $rapport->setRapportPdf($nom);
            $this->manager->persist($rapport);
            $this->manager->flush();
            foreach ($rapport->getPhotos() as $photo) {
                unlink('../public/uploads/photoRapport/' . $photo->getNom());
            }
        }
        return $this->render('rapport/attenteZip.html.twig', [
            'id' => $id
        ]);
    }

    /**
     * 
     *@Route("/RapportDemandeur/{rapport}",name="rapportDemandeur")
     * @isGranted("ROLE_DEMANDEUR")
     */
    public function RapportDemandeur($rapport)
    {

        $Rapport = $this->rapportRepository->findOneBy(['id' => $rapport]);

        return $this->render('rapport/rapport.html.twig', [
            'user' => $this->getUser(),
            'rapport' => $Rapport,
        ]);
    }

    /**
     * @Route("/telechargerMedia",name="telechargerMedia")
     *
     * @isGranted("ROLE_SALARIE")
     *
     * @param FactureCommisionInter $factureCom
     * @return JsonResponse
     * @throws MpdfException
     */
    public function telechargerMedia(FactureCommisionInter $factureCom,DefinirDate $definirDate)
    {
        $user = $this->getUser();
        $id = $this->request->getContent();

        $rapport = $this->rapportRepository->findOneBy(['id' => $id]);
        $videos = $rapport->getVideos();
        $nom = 'rapport';
        $cerfa = $rapport->getCerfaInter();
        $fichier = $rapport->getRapFichier();
        $assuranceOtd = $rapport->getIntervention()->getReservation()->getSalarie()->getEntreprise()->getEntAss()->getAssProFichier();

        $licenceOtd = $rapport->getIntervention()->getReservation()->getSalarie()->getLicenceDgac()->getFichierLicence();
        $donneeTelemetrique = $rapport->getDonneesTelemetrique();
        $photos = $rapport->getPhotos();
        if ($rapport->getIntervention()->getIntDem()->getUser()) {
            $nom = $rapport->getIntervention()->getIntDem()->getCivilite()->getNom();
        } else {
            $nom = $rapport->getIntervention()->getintDem()->getProfil() . $rapport->getIntervention()->getintDem()->getNom();
        }
        $responsable = $this->agentRepository->findResponsable($rapport->getIntervention()->getIntDem());
        $date = $rapport->getIntervention()->getRdvAt()->format('Y-m-d');
        $dirigeant = $this->salarieRepository->findDirirgeant($rapport->getIntervention()->getReservation()->getSalarie()->getEntreprise());



        $archive = $nom . $date . '.zip';
        $rapport->setArchive($archive);
        $zip = new ZipArchive();
        $zip->open($archive, ZipArchive::CREATE);
        if ($rapport->getIntervention()->getTypeInter()->getNom() !=="Captation audiovisuelle"){
            $zip->addFile("../public/uploads/rapport/".$rapport->getRapportPdf(),$rapport->getRapportPdf());
        }

        if ($photos) {
            foreach ($photos as  $photo) {
                $zip->addFile('../public/uploads/photoInter/' . $photo->getNom(), $photo->getNom());
            }
        }
        if ($videos) {
            foreach ($videos as $video) {
                $zip->addFile('../public/uploads/videoRapport/' . $video->getNom(), $video->getNom());
            }
        }
        if ($cerfa && $rapport->getIntervention()->getTypeInter()->getNom() !=="Captation audiovisuelle") {
            $zip->addFile('../public/uploads/cerfaInter/' . $cerfa, $cerfa);
        }
        if ($fichier && $rapport->getIntervention()->getTypeInter()->getNom() !=="Captation audiovisuelle") {
            $zip->addFile('../public/uploads/fichierRapport/' . $fichier, $fichier);
        }
        if ($donneeTelemetrique && $rapport->getIntervention()->getTypeInter()->getNom() !=="Captation audiovisuelle") {
            $zip->addFile('../public/uploads/fichierRapport/' . $donneeTelemetrique, $donneeTelemetrique);
        }

      // $zip->addFile('../public/uploads/assurances/'.$assuranceOtd,$assuranceOtd);
       // $zip->addFile('../public/uploads/licence/'.$licenceOtd,$licenceOtd);
        $zip->close();
        //readfile($archive);
        copy($archive, '../public/uploads/rapport/' . $archive);
        unlink($archive);

        if ($rapport->getIntervention()->getIntDem()->getUser()) {
            $factureCom->comInter($rapport->getIntervention());
        }
        $this->manager->persist($rapport);
        $this->manager->flush();
        $reponse = new JsonResponse();


        $route = '/entreprise';

        $reponse->setData([
            'reponse' => 'ok',
            'route' => $route
        ]);

        return $reponse;
    }

    /**
     * @Route("/upload/{id}")
     * @isGranted("ROLE_SALARIE")
     */
    public function uploadVideo($id, Fichier $fichier)
    {

        $rapport = $this->rapportRepository->findOneBy(['id' => $id]);
        if ($rapport->getIntervention()->getIntDem()->getCivilite()){
            $nom = $rapport->getIntervention()->getIntDem()->getCivilite()->getNom();
        }
        else{
            $nom= $rapport->getIntervention()->getIntDem()->getProfil().'-'.$rapport->getIntervention()->getIntDem()->getNom();
        }
        $nom1 = $nom.'1';
        $nom2 = $nom.'2';
        $destination = $this->videoDirectory;
        $video1 = $this->request->files->get('video1');
        $video2 = $this->request->files->get('video2');

        $fichier->saveVideo($video1, $rapport, $destination,$nom2);
        $fichier->saveVideo($video2, $rapport, $destination,$nom2);


        return new JsonResponse();
    }



}
