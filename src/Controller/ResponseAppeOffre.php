<?php


namespace App\Controller;



use App\Entity\Contact;
use App\Entity\DossierAo;
use App\Entity\DossierOtdAo;
use App\Entity\ReponseAo;
use App\Form\ReponseAoType;
use App\Helper\AppelOffreRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\ReponseAoRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\DossierOtdAoRepository;
use App\Repository\FichierInfoComplementaireRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;

use App\Service\Fichier;
use Exception as ExceptionAlias;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

/**
 * Class ResponseAppeOffre
 * @package App\Controller
 * @Security ("is_granted('ROLE_PREMIUM')")
 */
class ResponseAppeOffre extends AbstractController
{
    use AppelOffreRepoTrait,RequestTrait,EntityManagerTrait,SalarieRepoTrait,EntrepriseRepoTrait,ReponseAoRepoTrait;
    private string $appelOffreDirectory;


    /**
     * @param DefinirDate $definirDate
     * @param choixTemplate $choixTemplate
     * @return Response
     * @throws ExceptionAlias
     * @Route("/otd/listeAppelOffre",name="listeAppelOffreOtd")
     */
    public function recupererAppelOffre(DefinirDate $definirDate,choixTemplate $choixTemplate):Response{

        $appels = $this->appelOffreRepository->findForOtd($definirDate->aujourdhuiImmutable(),'publie');


        return $this->render('entreprise/listeAo.html.twig',[
            'appels'=>$appels,

        ]);

    }

    /**
     * @param $id
     * @param FichierInfoComplementaireRepository $repository
     * @Route("/entreprise/telechargerDossierAo/{id}",name="telechargerDossierAo")
     */
    public function telechargerDossierAo($id,FichierInfoComplementaireRepository $repository){
        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);
        $fichiers = $repository->findByAppel($appel);
        $nom = time().'.zip';
        $zip = new ZipArchive();
        $zip->open($nom,ZipArchive::CREATE);
        if (count($fichiers)>0){
            foreach ($fichiers as $fichier){
                $zip->addFile('../public/uploads/appelOffre/'.$fichier->getNom(),$fichier->getNom());
            }
        }
        foreach ($appel->getDossierAos() as $dossierAo){
            $zip->addFile('../public/uploads/appelOffre/'.$dossierAo->getFichier(),$dossierAo->getFichier());
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
     * @param int $id
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/entreprise/reponseAo-{id}/{idrep}",name="reponseAo")
     */
    public function reponseAppelOffre(int $id,choixTemplate $choixTemplate,int $idrep=null){

        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);
        if ($idrep){
            $reponse = $this->reponseAoRepository->findOneBy(['id'=>$idrep]);
        }
        else{
            $reponse = new ReponseAo();
            $reponse->setAppel($appel);
        }

        $form = $this->createForm(ReponseAoType::class,$reponse);
        $form->handleRequest($this->request);
        $template = $choixTemplate->definirTemplateEntrepriseSalarie($this->getUser());
        $connecte = $choixTemplate->definirConnecte($this->getUser());
        $reponse->setEntreprise($template[1]);
        if ($form->isSubmitted()&&$form->isValid()){
            $contactAo = new Contact();
            $contactAo->setReponseAo($reponse)
                ->setNom($connecte[1]->getNom())
                ->setPrenom($connecte[1]->getPrenom())
                ->setEmail($this->getUser()->getEmail())
                ->setTelephone($connecte[0]->getTelephone()->getNumero());
            $this->manager->persist($contactAo);
                foreach ($form['contacts']->getData() as $contact){
                    $contact->setReponseAo($reponse);
                    $this->manager->persist($contact);
                }

            $this->manager->persist($reponse);
            $this->manager->flush();
            return $this->redirectToRoute('ajoutDossierReponse',['id'=>$reponse->getId()]);
        }


        return $this->render('entreprise/reponseAo.html.twig',[

            'form'=>$form->createView(),
            'appel'=>$appel
        ]);
    }

    /**
     * @param int $id
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route ("entreprise/ajouteDossierReponse-{id}",name="ajoutDossierReponse")
     */
    public function ajoutDossierReponse(int $id,choixTemplate $choixTemplate):Response{

        $reponse = $this->reponseAoRepository->findOneBy(['id'=>$id]);
        return $this->render('entreprise/dossierReponseAo.html.twig',[
            'id'=>$id,

            'reponse'=>$reponse
        ]);

    }

    /**
     * @param int $id
     * @param Fichier $fichier
     * @return JsonResponse
     * @Route("/entreprise/dossierAo/{id}")
     */
    public function saveDossierAo(int $id,Fichier $fichier):JsonResponse{
        $reponseAo = $this->reponseAoRepository->findOneBy(['id'=>$id]);
        $file = $this->request->files->get('file');
        $nom = 'dossier reponse'.$reponseAo->getAppel()->getType().time();
        $filename = $fichier->saveFile($nom,$this->getParameter('appelOffre_directory'),$file);
        $dossier = new DossierOtdAo();
        $dossier->setReponseAo($reponseAo)
            ->setFichier($filename);
        $this->manager->persist($dossier);
        $this->manager->flush();

        return new JsonResponse($dossier->getId());

    }

    /**
     * @param int $id
     * @param DossierOtdAoRepository $repository
     * @return JsonResponse
     * @Route ("/entreprise/supprimerDossier/{id}")
     */
    public function effacerDossierAo(int $id,DossierOtdAoRepository $repository):JsonResponse{
       $dossier = $repository->findOneBy(['id'=>$id]);

        $this->manager->remove($dossier);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     * @throws ExceptionAlias
     * @Route("/entreprise/Mesreponses",name="mesReponseAo")
     */
    public function mesReponse(DefinirDate $definirDate){
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $reponse = $this->reponseAoRepository->findByStatuEntrepriseDate($salarie->getEntreprise(),$definirDate->aujourdhui(),'publie');

        return $this->render('entreprise/mesReponseAo.html.twig',[
            'reponses'=>$reponse
        ]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @Route("/entreprise/supprimerReponse-{id}",name="supprimerReponse")
     */
    public function supprimerReponse(int $id):RedirectResponse{
        $reponse = $this->reponseAoRepository->findOneBy(['id'=>$id]);
        foreach ($reponse->getDossierOtdAos() as $dossier){
            $this->manager->remove($dossier);
            unlink('../public/uploads/appelOffre/'.$dossier->getFichier());
        }
        foreach ($reponse->getContacts() as $contact){
            $this->manager->remove($contact);
        }
        $this->manager->flush();
        $this->manager->remove($reponse);
        $this->manager->flush();
        return $this->redirectToRoute('listeAppelOffreOtd');
    }

    /**
     * @return Response
     * @Route("/entreprise/mes appels d'offre",name="mesAppel")
     */
    public function mesAppelOffre(){
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $reponses = $this->reponseAoRepository->findByEntreprise($salarie->getEntreprise());

        return $this->render('entreprise/mesAppelDoffre.html.twig',[
            'reponses'=>$reponses
        ]);
    }
}