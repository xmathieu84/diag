<?php

namespace App\Controller;




use App\Entity\Annotation;
use App\Entity\Contact;
use App\Entity\DocSousDossier;
use App\Entity\Dossier;
use App\Entity\DossierGeneral;
use App\Entity\SousDossier;
use App\Form\DossierType;
use App\Helper\AgentRepoTrait;
use App\Helper\DocSouDossierRepoTrait;
use App\Helper\DossierRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\ListeInterRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SousDossierRepoTrait;
use App\Repository\AnnotationRepository;
use App\Repository\DemandeurRepository;
use App\Service\choixTemplate;
use App\Service\Fichier;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZipArchive;


class DossierController extends AbstractController
{
    use EntityManagerTrait,RequestTrait,AgentRepoTrait,DossierRepoTrait,ListeInterRepoTrait,SousDossierRepoTrait,DocSouDossierRepoTrait;


    /**
     * @Route("/institution/batiment", name="batiment")
     * @Security ("is_granted('ROLE_INSTITUTION')")
     */
    public function index(): Response
    {
        return $this->render('institution/batiment.html.twig');
    }

    /**
     * @param
     * @return JsonResponse
     * @Route ("/institution/rechercheBatiment")
     * @Security ("is_granted('ROLE_INSTITUTION')")
     */
    public function rechercheBatiment():JsonResponse{

        return new JsonResponse();
    }

    /**
     * @Route("/création d'un dossier/{code}",name="creerDossier")
     *
     * @Security(" is_granted('ROLE_NIVEAU1')")
     * @param choixTemplate $choixTemplate
     * @return Response
     * @throws Exception
     */
    public function creerActif(choixTemplate $choixTemplate,string $code=null):Response{
        if (!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }
        else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }


        $dossier = new Dossier();
        $form = $this->createForm(DossierType::class,$dossier);
        $form->handleRequest($this->request);
        if ($form->isSubmitted()&&$form->isValid()){
            $dossier->setDateModif(New DateTime('NOW',new DateTimeZone('Europe/Paris')))
                ->setCreateur($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
                ->setTypeModif('Création')
                ->setProprietaire($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
                ->setNomModifiant($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
                ->setInstitution($agent->getDemandeur());
            if ($form['dossierGen']->getData()){
                $dossierGen = new DossierGeneral();
                $dossier->setDossierGeneral($dossierGen);
                $this->manager->persist($dossierGen);
            }
            $this->manager->persist($dossier);
            $this->manager->flush();
            if ($form['dossierGen']->getData()){
                return  $this->redirectToRoute('dossierGeneral',[
                    'nom'=>$dossier->getType(),
                    'code'=>$code,
                    'id'=>$dossier->getId()
                ]);
            }
            else{
                return $this->redirectToRoute('FinaliserDossier',[
                    'code'=>$code,
                    'nom'=>$dossier->getType(),
                    'id'=>$dossier->getId()
                ]);
            }

        }


        return $this->render('institution/creerActif.html.twig',[

            'form'=>$form->createView(),
            'code'=>$code

        ]);
    }

    /**
     * @param string|null $code
     * @return Response
     * @Route("/liste dossier/{code}",name="listeDossier")
     */
    public function listeDossier(string $code=null){
        if (!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }
        else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        $institution = $agent->getDemandeur();
        $patrimoine = $this->dossierRepository->findBy(['institution'=>$institution,'type'=>'Patrimoine immobilier']);
        $espace = $this->dossierRepository->findBy(['institution'=>$institution,'type'=>'Voirie , Espace vert , Parking']);
        $ouvrage = $this->dossierRepository->findBy(['institution'=>$institution,'type'=>'Ouvrage d’art']);
        $reseau = $this->dossierRepository->findBy(['institution'=>$institution,'type'=>'Réseaux']);
        $sport = $this->dossierRepository->findBy(['institution'=>$institution,'type'=>'Evènement sportif ou culturel']);
        $inter = $this->dossierRepository->findBy(['institution'=>$institution,'type'=>'Intervention diverse']);
        return $this->render('institution/listeDossier.html.twig',[
            'code'=>$code,
            'patrimoine'=>count($patrimoine),
            'espace'=>count($espace),
            'ouvrage'=>count($ouvrage),
            'reseau'=>count($reseau),
            'sport'=>count($sport),
            'inter'=>count($inter),


        ]);
    }

    /**
     *
     * @param string $nom
     * @param string|null $code
     * @return Response
     * @Route("/listeDossier/{nom}/{code}",name="indossier")
     */
    public function inDossier(string $nom,string $code=null){
        if(!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }
        else{
            $agent  =$this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        $dossiers = $this->dossierRepository->findBy(['type'=>$nom,'institution'=>$agent->getDemandeur()]);

        return $this->render('institution/typeDossier.html.twig',[
            'dossiers'=>$dossiers,
            'code'=>$code



        ]);
    }

    /**
     * @param int $id
     * @param choixTemplate $choixTemplate
     * @param AnnotationRepository $repo
     * @param string|null $code
     * @return Response
     * @Route("/liste dossier/{nom}/{id}/{code}",name="FinaliserDossier")
     *
     * @Security("is_granted('ROLE_NIVEAU1')")
     */
    public function inSousDossier(int $id,AnnotationRepository $repo,string $code=null):Response
    {
        $dossier = $this->dossierRepository->findOneBy(['id'=>$id]);
        $notes = $repo->findByDossier($dossier);

        $interventions = $this->listeInterRepository->findAll();
        $dossierRange = [];
        foreach ($dossier->getSousDossiers() as $sousDossier){
            foreach ($sousDossier->getDocSousDossiers() as $docSousDossier){
                $dossierRange[] = [
                    'type' => $docSousDossier->getSousDossier()->getType(),
                    'date'=>$docSousDossier->getDate()->format('d-m-Y'),
                    'nom'=>$docSousDossier->getLibelle(),
                    'alerte'=>$docSousDossier->getDelaiAlerte(),
                    'piece'=>$docSousDossier,
                    //'dateA'=>$docSousDossier->getDate()->add($docSousDossier->getDelaiAlerte())
                ];
            }
        }
        if ($dossier->getDossierGeneral()){
            foreach ($dossier->getDossierGeneral()->getPiecesgenerales() as $piecesgenerale){
                $dossierRange[] = [
                    'type' => 'Pièce générale',
                    'date'=>$piecesgenerale->getDate()->format('d-m-Y'),
                    'nom'=>$piecesgenerale->getNom(),
                    'alerte'=>null,
                    'piece'=>$piecesgenerale,
                    //'dateA'=>null

                ];
            }
        }

        array_multisort(array_column($dossierRange,'date'),SORT_DESC,$dossierRange);

        return $this->render('institution/sousDossier.html.twig', [
            'dossier'=>$dossier,
            'code'=>$code,
            'interventions'=>$interventions,
            'Rdossiers'=>$dossierRange,
            'notes'=>$notes
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/createDossierGenarale/{id}")
     */
    public function createDossierGeneral(int $id){
        $dossier = $this->dossierRepository->findOneBy(['id'=>$id]);
        $dossier->setDossierGeneral(new DossierGeneral());
        $this->manager->persist($dossier);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/institution/creerSousDossier/{id}")
     */
    public function createSousDos(int $id){
        $type = $this->request->getContent();
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $dossier = $this->dossierRepository->findOneBy(['id'=>$id]);
        $dossier->setNomModifiant($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
            ->setDateModif(new DateTime('NOW',new DateTimeZone('Europe/Paris')))
            ->setTypeModif('Création du sous dossier '.$type);
        $sousDossier = new SousDossier();
        $sousDossier->setType($type)
                ->setDossier($dossier);
       $this->manager->persist($sousDossier);
       $this->manager->persist($dossier);
       $this->manager->flush();
        return new JsonResponse();
    }

    /**
     *
     * @param string $type
     * @param int $id
     * @param string|null $code
     * @return Response
     * @Route("/liste annontation/{id}/{type}/{code}",name="docSousDossier")
     */
    public function docSousDossier(string $type,int $id,string $code=null):Response{
        if (!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }

        $dossier = $this->dossierRepository->findOneBy(['id'=>$id]);
        $sousDossier = $this->sousDossierRepository->findByInstitution($agent->getDemandeur(),$type,$dossier);

        if ($type === 'Plan' || $type ==='Diagnostic technique'){
            $accept ='application/pdf';

        } elseif ($type==='Photo'){
            $accept ="image/*";
        }
        else{
            $accept =".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,video/*,application/pdf,image/*";
        }

        return $this->render('institution/docSousDossier.html.twig',[

            'sousDossier'=>$sousDossier,
            'accept'=>$accept,
            'code'=>$code
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/envoieDocInstitution-{id}")
     * @throws Exception
     */
    public function addDoc(int $id):JsonResponse{
        $sousDossier = $this->sousDossierRepository->findOneBy(['id'=>$id]);
        $content = json_decode($this->request->getContent(),true);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $doc = new DocSousDossier();
        $date = null;
        $delai = null;
        $sousDossier->getDossier()->setTypeModif("Ajout d'un document")
            ->setNomModifiant($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
            ->setDate(new DateTime('NOW',new DateTimeZone('Europe/Paris')));
        if ($content['validite']){
            $date = DateTime::createFromFormat('Y-m-d',$content['validite']);

        }
        if ($content['alerte']){
            $delai = new DateInterval($content['alerte']);
        }
        $doc->setDateDevalidite($date)
            ->setDelaiAlerte($delai)
            ->setLibelle($content['lib'])
            ->setSousDossier($sousDossier);
        $this->manager->persist($doc);
        $this->manager->flush();
        return new JsonResponse($doc->getId());
    }

    /**
     * @param int $id
     * @param Fichier $fichier
     * @return JsonResponse
     * @Route("/envoieFichierDoc/{id}")
     */
    public function addFile(int $id,Fichier $fichier):JsonResponse{
        $doc = $this->docSousDossierRepository->findOneBy(['id'=>$id]);
        $file=  $this->request->files->get('file');

        $fileName = $fichier->saveFile(time(),$this->getParameter('dossier_directory'),$file);
        $doc->setFichier($fileName);
        $this->manager->persist($doc);
        $this->manager->flush();
        return new JsonResponse($doc->getId());

    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route ("/ajoutContact/{id}")
     */
    public function addContact(int $id){
        $doc = $this->docSousDossierRepository->findOneBy(['id'=>$id]);
        $content = json_decode($this->request->getContent(),true);
        $pattern = ['/<script>/','/--/','/#/'];
        $contact = new Contact();
        $contact->setNom(preg_replace($pattern,'',$content['nom']))
            ->setPrenom(preg_replace($pattern,'',$content['prenom']))
                ->setTelephone(preg_replace($pattern,'',$content['telephone']))
                    ->setEmail(preg_replace($pattern,'',$content['email']))
            ->setDocSousDossier($doc);
        $this->manager->persist($contact);
        $this->manager->flush();
        return new JsonResponse($doc->getId());
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     * @Route("/ajoutNote/{id}")
     */
    public function addAnnotation(int $id):JsonResponse{
        $doc = $this->docSousDossierRepository->findOneBy(['id'=>$id]);
        $connecte = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $content = json_decode($this->request->getContent(),true);
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $doc->getSousDossier()->getDossier()->setTypeModif("Ajout d'un document")
            ->setNomModifiant($agent->getCivilite()->getNom().' '.$agent->getCivilite()->getPrenom())
            ->setDate(new DateTime('NOW',new DateTimeZone('Europe/Paris')));
        $pattern = ['/<script>/','/--/','/#/'];
        $annotation = new Annotation();
        $auteur = $connecte->getCivilite()->getNom().' '.$connecte->getCivilite()->getPrenom();
        $annotation->setDocSousDossier($doc)
            ->setTexte(preg_replace($pattern,'',$content['texteNote']))
            ->setAuteur($auteur)
            ->setDate(New DateTime('NOW',New DateTimeZone('Europe/Paris')))
            ->setTitre(preg_replace($pattern,'',$content['titreNote']));
        $this->manager->persist($doc);
        $this->manager->persist($annotation);
        $this->manager->flush();
        return new JsonResponse();

    }

    /**
     * @param int $id
     * @Route ("/uploadsPieces/{id}",name="uploadPiece")
     */
    public function uploadPiece(int $id){
        $dossier = $this->dossierRepository->findOneBy(['id'=>$id]);
        $pieces = $dossier->getDossierGeneral()->getPiecesgenerales();
        $docs = $this->docSousDossierRepository->findByDossier($dossier);
        $nom = time().'.zip';
        $zip = new ZipArchive();
        $zip->open($nom,ZipArchive::CREATE);

            foreach ($pieces as $piece){

                $zip->addFile('../public/uploads/dossier/'.$piece->getFichier(),$piece->getFichier());
            }


            foreach ($docs as $doc){
                $zip->addFile('../public/uploads/dossier/'.$doc->getFichier(),$doc->getFichier());
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
     * @return JsonResponse
     * @Route("/deleteSousDossier")
     */
    public function deleteSousDossier():JsonResponse{
        $sousDossier = $this->sousDossierRepository->findOneBy(['id'=>$this->request->getContent()]);
        foreach ($sousDossier->getDocSousDossiers() as $docSousDossier){

            foreach ($docSousDossier->getContact() as $contact){

                $this->manager->remove($contact);
            }
            foreach ($docSousDossier->getAnnotations() as $annotation){
               $this->manager->remove($annotation);
            }
            unlink("../public/uploads/dossier/".$docSousDossier->getFichier());
            $this->manager->remove($docSousDossier);
        }
        $this->manager->remove($sousDossier);
        $this->manager->flush();

        return new JsonResponse();
    }



}
