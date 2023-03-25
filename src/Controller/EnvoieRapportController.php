<?php


namespace App\Controller;


use App\Entity\ConsultantHDD;
use App\Helper\EntityManagerTrait;
use App\Helper\RapportRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\ConsultantHDDRepository;
use App\Repository\DemandeurRepository;
use App\Service\choixTemplate;
use App\Service\codeActivation;
use App\Service\Mail;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EnvoieRapportController extends AbstractController
{
    use RapportRepoTrait,RequestTrait,EntityManagerTrait;

    /**
     * @param choixTemplate $choixTemplate
     * @param DemandeurRepository $demandeurRepository
     * @return Response
     * @Route("/listeRapport",name="listeRapport")
     * @Security("is_granted('ROLE_NIVEAU1') or is_granted('ROLE_DEMANDEUR')")
     */
    public function listeRapport(choixTemplate $choixTemplate,DemandeurRepository $demandeurRepository):Response{

        $template = $choixTemplate->templateDem($this->getUser());
        $demandeur = $template[1];

            $rapports = $this->rapportRepository->findForEnvoieDemandeur($demandeur);

        return $this->render('rapport/listeRapport.html.twig',[
            'template'=>$template[0],
            'rapports'=>$rapports
        ]);
    }

    /**
     * @param codeActivation $codeActivation
     * @param int $id
     * @param Mail $mail
     * @return JsonResponse
     * @Route ("/receptionMailRapport/{id}")
     * @Security("is_granted('ROLE_NIVEAU1') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CLASSIC')")
     */
    public function receptionMailRapport(codeActivation $codeActivation,int $id,Mail $mail){

        $rapport = $this->rapportRepository->findOneBy(['id'=>$id]);
        $content = json_decode($this->request->getContent());

        if ($content->reference===""){
            $reference =null;
        }
        else{
            $reference = $content->reference;
        }
        $codeRecherche = $codeActivation->codeAcces();
        $rapport->setCodeRecherche($codeRecherche);

        foreach ($content->email as $email){
            $codeUnique = $codeActivation->codeRapport();
            $consultant = new ConsultantHDD();
            $consultant->setMail($email)
                ->setCodeUnique($codeUnique)
                ->setRapport($rapport);
            $this->manager->persist($consultant);
            $this->manager->flush();
            $mail->mailConsultant($email,$codeRecherche,$codeUnique,$reference);

        }
        $this->manager->persist($rapport);
        $this->manager->flush();
        return new JsonResponse();

    }

    /**
     * @Route("/media")
     *
     *
     * @param ConsultantHDDRepository $consultantHDDRepository
     * @return JsonResponse
     */
     public function telechargerRapport( ConsultantHDDRepository $consultantHDDRepository):JsonResponse
    {
        $content = json_decode($this->request->getContent());

        $consultant = $consultantHDDRepository->findOneBy(['codeUnique'=>$content->codeRapport,'mail'=>$content->mail]);
        $reponse =  new JsonResponse();
        if ($consultant){
            $archive = $consultant->getRapport()->getArchive();
            $liste=[];
            $listeVideo=[];
            $listePhoto = [];

            foreach ($consultant->getRapport()->getVideos() as $video){
                $listeVideo[]=$video->getNom();
            }
            foreach ($consultant->getRapport()->getPhotos() as$photo){
                $listePhoto[]=$photo->getNom();
            }
            array_push($liste,['rapport'=>$consultant->getRapport()->getRapportPdf(),'archive'=>$consultant->getRapport()->getArchive(),'photos'=>$listePhoto,'videos'=>$listeVideo]);
            $consultant->setCodeUnique(null);
            $reponse->setData($liste);
            $this->manager->persist($consultant);
            $this->manager->flush();
        }
        else{
            $reponse->setData('non');
        }
        return $reponse;


    }

    /**
     * @return Response
     * @Route("/rechercheRapport",name="rechercheRapport")
     */
    public function rechercheRapportUnique(){

         return $this->render('accueil/rechercheRapport.html.twig');
    }



}