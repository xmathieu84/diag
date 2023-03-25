<?php


namespace App\Controller;


use App\Entity\DonneesGenerales;
use App\Entity\NoteGen;
use App\Entity\Piecesgenerale;
use App\Helper\AgentRepoTrait;
use App\Helper\DossierGenRepoTrait;
use App\Helper\DossierRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\DonneesGeneralesRepository;
use App\Repository\NoteGenRepository;
use App\Repository\PiecesgeneraleRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Fichier;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DossierGeneralController extends AbstractController
{
    use DossierRepoTrait,DossierGenRepoTrait,RequestTrait,EntityManagerTrait,AgentRepoTrait;

    /**
     * @param choixTemplate $choixTemplate
     * @param int $id
     * @return Response
     * @Route("/institution/dossier general/{nom}/{id}/{code}",name="dossierGeneral")
     * @Route("/grandCompte/dossier general",name="dossierGeneralGc")
     */
    public function inDossierGeneral(choixTemplate $choixTemplate,int $id,string $code=null):Response{

        $dossier = $this->dossierRepository->findOneBy(['id'=>$id]);

        return $this->render('institution/dossierGeneral.html.twig',[
            'dossierGeneral'=>$dossier->getDossierGeneral(),
            "code"=>$code
        ]);
    }

    /**
     * @param int $id
     * @return void
     * @throws Exception
     * @Route("/ajoutNoteGenerale/{id}")
     */
    public function ajoutNote(int $id):void{
        $dossierGen =$this->dossierGeneralRepository->findOneBy(['id'=>$id]);
        $contenu =json_decode($this->request->getContent());
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);

        $dossierGen->getDossier()->setTypeModif("Ajout d'une note générale")
            ->setNomModifiant($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
            ->setDate(new DateTime('NOW',new DateTimeZone('Europe/Paris')));
        $pattern = ['/<script>/','/--/','/#/'];
        $connecte = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $auteur = $connecte->getCivilite()->getNom().' '.$connecte->getCivilite()->getPrenom();
        $note = new NoteGen();
        $note->setTitre(preg_replace($pattern,'',$contenu->titre))
            ->setTexte(preg_replace($pattern,'',$contenu->texte))
            ->setAuteur($auteur)
            ->setDate(new \DateTime('NOW',New \DateTimeZone('Europe/Paris')))
            ->setDossierGeneral($dossierGen);
        $this->manager->persist($note);
        $this->manager->flush();

        exit;
    }

    /**
     * @param int $id
     * @param Fichier $fichier
     * @return void
     * @Route ("/ajoutDocGeneral/{id}")
     */
    public function ajoutDocGeneral(int $id,Fichier $fichier):void{
        $dossierGen =$this->dossierGeneralRepository->findOneBy(['id'=>$id]);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $dossierGen->getDossier()->setTypeModif("Ajout d'un document au dossier général")
            ->setNomModifiant($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
            ->setDate(new DateTime('NOW',new DateTimeZone('Europe/Paris')));
        $file = $this->request->files->get('docGeneral');
        $filename = $fichier->saveFile(time(),$this->getParameter('dossier_directory'),$file);
        $nom = $this->request->request->get('nomFichier');
        $piece = new Piecesgenerale();
        $piece->setNom($nom)
            ->setFichier($filename)
            ->setDossierGeneral($dossierGen);
        $this->manager->persist($piece);
        $this->manager->flush();

        exit();
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route ("/ajoutDonneeGenerale/{id}")
     */
    public function ajoutDonneeGenerale(int $id):JsonResponse{
        $dossierGen =$this->dossierGeneralRepository->findOneBy(['id'=>$id]);
        $content = json_decode($this->request->getContent());
        $pattern = ['/<script>/','/--/','/#/'];
        $donneeGen = new DonneesGenerales();
        $donneeGen->setComplement(preg_replace($pattern,'',$content->complement))
        ->setInformation(preg_replace($pattern,'',$content->information))
        ->setJuridique(preg_replace($pattern,'',$content->juridique))
        ->setPresentation(preg_replace($pattern,'',$content->presentation))
        ->setFinance(preg_replace($pattern,'',$content->finance))
        ->setIntervenant(preg_replace($pattern,'',$content->intervenant));
        $dossierGen->setDonneeGenerale($donneeGen);
        $this->manager->persist($donneeGen);
        $this->manager->persist($dossierGen);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param DonneesGeneralesRepository $repository
     * @return JsonResponse
     * @Route("/modifDonneeGenerale")
     */
    public function modifDonneeGenerale(DonneesGeneralesRepository $repository):JsonResponse{
        $content = json_decode($this->request->getContent());
        $donneGen = $repository->findOneBy(['id'=>$content->id]);
        $pattern = ['/<script>/','/--/','/#/'];
        $texte = preg_replace($pattern,'',$content->texte);
        switch ($content->typeChange){
            case 'presentation':
            $donneGen->setPresentation($texte);
            case 'information':
                $donneGen->setInformation($texte);
            case 'intervenant':
                $donneGen->setIntervenant($texte);
            case 'finance':
                $donneGen->setFinance($texte);
            case 'juridique':
                $donneGen->setJuridique($texte);
            case 'complement':
                $donneGen->setComplement($texte);

        }
        $this->manager->persist($donneGen);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param NoteGenRepository $repository
     * @return JsonResponse
     * @Route("/effacerNote")
     */
     public function effacerNote(NoteGenRepository $repository,DefinirDate $definirDate):JsonResponse{
        $id = $this->request->getContent();
        $agent = $this->getUser()->getAgent();
        $note = $repository->findOneBy(['id'=>$id]);
        $dossier =  $note->getDossierGeneral()->getDossier();
        $dossier->setDateModif($definirDate->aujourdhui())
            ->setNomModifiant($agent->getCivilite()->getPrenom().' '.$agent->getCivilite()->getNom())
            ->setTypeModif("Suppression d'une note générale");
        $this->manager->remove($note);
        $this->manager->persist($dossier);
        $this->manager->flush();

        return new JsonResponse();
     }

    /**
     * @param PiecesgeneraleRepository $repository
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @Route("/supprimerPieceGen")
     */
     public function supprimerPieceGen(PiecesgeneraleRepository $repository,DefinirDate $definirDate):JsonResponse{
        $id = $this->request->getContent();
        $piece = $repository->findOneBy(['id'=>$id]);
        $dossier = $piece->getDossierGeneral()->getDossier();

        return new JsonResponse();
     }

    /**
     * @return JsonResponse
     * @Route("/deleteDossierGen")
     */
     public function deleteDossierGeneral():JsonResponse{
         $dossierGen = $this->dossierGeneralRepository->findOneBy(['id'=>$this->request->getContent()]);
         $dossierGen->getDossier()->setDossierGeneral(null);
         $this->manager->persist($dossierGen->getDossier());
         $this->manager->flush();
         foreach ($dossierGen->getNoteGens() as $noteGen){
             $this->manager->remove($noteGen);
         }
         foreach ($dossierGen->getPiecesgenerales() as $piecesgenerale){
             $this->manager->remove($piecesgenerale);
             unlink('../uploads/dossier/'.$piecesgenerale->getFichier());
         }
         $this->manager->remove($dossierGen->getDonneeGenerale());
         $this->manager->remove($dossierGen);
         $this->manager->flush();

         return new JsonResponse();
     }
}