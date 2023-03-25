<?php


namespace App\Controller;


use App\Helper\AppelOffreRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\ReponseAoRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\choixTemplate;
use App\Service\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

class RepsAppelOffreController extends AbstractController
{
    use AppelOffreRepoTrait,ReponseAoRepoTrait,EntrepriseRepoTrait,EntityManagerTrait,SalarieRepoTrait;

    /**
     * @param int $id
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/reponse appel d'offre-{id}/{code}",name="reponseAoInsti")
     *
     */
    public function voirReponse(int $id,choixTemplate $choixTemplate,string $code=null){
        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);
        $template = $choixTemplate->templateDem($this->getUser());
        return $this->render('institution/reponseAo.html.twig',[
           'appel'=>$appel,
            'template' =>$template[0],
            'code'=>$code
        ]);
    }

    /**
     * @param int $id
     * @Route("/uploadDossierAo-{id}",name="uploadDossierAo")
     */
    public function uploadReponse(int $id){
        $reponse = $this->reponseAoRepository->findOneBy(['id'=>$id]);
        $nom = time().'zip';
        $zip = new ZipArchive();
        $zip->open($nom,ZipArchive::CREATE);
        foreach ($reponse->getDossierOtdAos() as $dossier){
            $zip->addFile('../public/uploads/appelOffre/'.$dossier->getFichier(),$dossier->getFichier());
        }
        $zip->close();
        header('Content-Description: File Transfer');
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: application/zip");
        header("Content-Disposition: attachment; filename=".$nom);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($nom));
        readfile($nom);
        unlink($nom);
        exit;
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @Route ("/accpeterReponse/{id}/{code}", name="accpeterReponse")
     */
    public function choixReponse($id,Mail $mail,string $code=null){
        $reponse = $this->reponseAoRepository->findOneBy(['id'=>$id]);
        $appel = $reponse->getAppel();
        $appel->setReponseChoisie($reponse)
                ->setEtat('reponseChoisie');
        $dirigeant = $this->salarieRepository->findDirirgeant($reponse->getEntreprise());
        $mail->mailAppelOffre($dirigeant->getUser()->getEmail(),$appel);
        $this->manager->persist($appel);
        $this->manager->flush();
        return $this->redirectToRoute('mesAO');

    }



}