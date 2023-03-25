<?php

namespace App\Controller;

use App\Entity\CampagneMail;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\AdminBienRepository;
use App\Repository\AgentAssuRepository;
use App\Repository\AgentImmoRepository;
use App\Repository\BureauEtudeRepository;
use App\Repository\CampagneMailRepository;
use App\Repository\ContactBtpRepository;
use App\Repository\ControleBatRepository;
use App\Repository\EntrepriseTpRepository;
use App\Repository\EquipeRepository;
use App\Repository\ExpertAssuRepository;
use App\Repository\FichierOTDRepository;
use App\Repository\GeometresExpertsRepository;
use App\Repository\JournalisteRepository;
use App\Repository\LotisseursRepository;
use App\Repository\MaitresDOeuvreEnBatimentRepository;
use App\Repository\OfficesEtGestionDHlmRepository;
use App\Repository\PromoteursConstructeursRepository;
use App\Repository\UrbanistesRepository;
use App\Repository\VilleMailingRepository;
use App\Service\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class AdminMailPromoController extends AbstractController
{
    use RequestTrait,EntityManagerTrait;
    /**
     * @return Response
     * @Route ("/administrateur/envoieOtd",name="envoieOtd")
     */
    public function envoieOtd(CampagneMailRepository $campagneMailRepository):Response{
        $campagnes = $campagneMailRepository->findBy(['cible'=>"otd"]);
        return $this->render('administrateur/envoieOtd.html.twig',[
            'campagnes'=>$campagnes
        ]);
    }

    /**
     * @param Mail $mail
     * @param FichierOTDRepository $repository
     * @return JsonResponse
     * @Route ("/administrateur/mailPartOtd")
     */
    public function mailPartOtd(Mail $mail,FichierOTDRepository $repository):JsonResponse{
        $content = json_decode($this->request->getContent());

        if ($content->code ===""){
            $code = null;
        }
        else{
            $code = $content->code;
        }
        $otds = $repository->findBy(['desabonner'=>false]);
        $liste = ['coquard.dominique@gmail.com','contact@diag-drone.com','marinvincent84000@hotmail.fr',"mathieumiranda@hotmail.com","axel.cloux@gmail.com"];
        $i=0;
       foreach ($otds as $key => $otd){

               if ($otd->getMail()){
                   $i++;
                   $mail->mailLancement($otd->getMail(),$content->titre,$content->para,$code);

           }



       }
         for ($i=0, $iMax = count($liste); $i< $iMax; $i++){
            $mail->mailLancement($liste[$i],$content->titre,$content->para,$code);
        }
        $campagne = new CampagneMail();
        $campagne->setCible("otd")
            ->setDate(new \DateTime())
            ->setDescription($content->description)
            ->setTitreEmail($content->titre)
            ->setCodePromo($code)
            ->setType('otd')
            ->setContenu($content->para)
            ->setMailEnvoye($i);
        $this->manager->persist($campagne);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     *
     * @return Response
     * @Route("/seDesabonner",name="seDesabonner")
     */
    public function seDesabonner(){

        return $this->render('accueil/desobonner.html.twig');
    }

    /**
     * @param FichierOTDRepository $repository
     * @return JsonResponse
     * @Route("/desabonnementOtd")
     */
    public function desabonnementOtd(FichierOTDRepository $repository):JsonResponse{
        $content = $this->request->getContent();
        $otd = $repository->findOneBy(['mail'=>$content]);
        $reponse = new JsonResponse();
        if ($otd && $otd->getDesabonner() ==false){
            $otd->setDesabonner(true);
            $this->manager->persist($otd);
            $this->manager->flush();
            return $reponse->setData('Votre désabonnement est confirmé .');
        }
        elseif ($otd && $otd->getDesabonner() ==="true"){
            return $reponse->setData('Votre êtes déjà desabonné .');
        }
        else{
            return $reponse->setData("Cette adresse n'existe pas .");
        }
    }

    /**
     * @return Response
     * @Route("/administrateur/autreEnvoie",name="autreEnvoie")
     */
    public function autreEnvoie(CampagneMailRepository $campagneMailRepository){
        $campagnes = $campagneMailRepository->findBy(['type'=>"autre"]);
        return $this->render('administrateur/autreEnvoie.html.twig',[
            'campagnes'=>$campagnes
        ]);
    }

    /**
     * @param ContactBtpRepository $contactBtpRepository
     * @param AdminBienRepository $adminBienRepository
     * @param AgentImmoRepository $agentImmoRepository
     * @param BureauEtudeRepository $bureauEtudeRepository
     * @param ControleBatRepository $controleBatRepository
     * @param EntrepriseTpRepository $entrepriseTpRepository
     * @param ExpertAssuRepository $expertAssuRepository
     * @param GeometresExpertsRepository $geometresExpertsRepository
     * @param MaitresDOeuvreEnBatimentRepository $oeuvreEnBatimentRepository
     * @param OfficesEtGestionDHlmRepository $officesEtGestionDHlmRepository
     * @param PromoteursConstructeursRepository $promoteursConstructeursRepository
     * @param UrbanistesRepository $urbanistesRepository
     * @param LotisseursRepository $lotisseursRepository
     *
     * @return JsonResponse
     * @Route("/administrateur/envoieMailAutre")
     */
    public function envoieMailAutre(ContactBtpRepository           $contactBtpRepository, AdminBienRepository $adminBienRepository, AgentImmoRepository $agentImmoRepository,
                                    BureauEtudeRepository          $bureauEtudeRepository, ControleBatRepository $controleBatRepository, EntrepriseTpRepository $entrepriseTpRepository,
                                    ExpertAssuRepository           $expertAssuRepository, GeometresExpertsRepository $geometresExpertsRepository, MaitresDOeuvreEnBatimentRepository $oeuvreEnBatimentRepository,
                                    OfficesEtGestionDHlmRepository $officesEtGestionDHlmRepository, PromoteursConstructeursRepository $promoteursConstructeursRepository, UrbanistesRepository $urbanistesRepository,
                                    LotisseursRepository           $lotisseursRepository,AgentAssuRepository $agentAssuRepository,Mail $mail,EquipeRepository $equipeRepository,VilleMailingRepository $villeMailingRepository):JsonResponse{
        $content = json_decode($this->request->getContent());
        $repo = null;
        $cible = null;
        $departements = null;
        if ($content->departement !==""){
            $departements = explode(",",$content->departement);
        }

        $mail->mailLancementAutre('mathieumiranda@hotmail.com',$content->titre,$content->content,$content->code,5,$content->cible);
       switch ($content->cible){
            case 'abien' :
                $repo = $adminBienRepository;
                $cible = "Administrateur de bien et syndic";
                break;
           case 'equipe' :
               $repo = $equipeRepository;
                $cible = "Mail test";
               break;
            case 'agentAssu' :
                $repo = $agentAssuRepository;
                $cible = "Agents d'assurances";
                break;
            case 'agentImmo' :
                $repo = $agentImmoRepository;
                $cible = "Agents immobilier";
                break;
            case 'bureauEtude' :
                $repo = $bureauEtudeRepository;
                $cible = "Bureau d'étude";
                break;
            case 'btp' :
                $repo = $contactBtpRepository;
                $cible = "BTP";
                break;
            case 'controlBtp' :
                $repo = $controleBatRepository;
                $cible = "Controlleurs du bâtiments";
                break;
            case 'tp' :
                $repo = $entrepriseTpRepository;
                $cible = "Entreprise travaux public";
                break;
            case 'geometre' :
                $repo = $geometresExpertsRepository;
                $cible = "Géomètres";
                break;
            case 'maitre' :
                $repo = $oeuvreEnBatimentRepository;
                $cible = "Maitre d'oeuvre";
                break;
            case 'hlm' :
                $repo = $officesEtGestionDHlmRepository;
                $cible = "Office de gestion HLM";
                break;
            case 'promoteur' :
                $repo = $promoteursConstructeursRepository;
                $cible = "Promoteurs et constructeurs";
                break;
            case 'urbaniste' :
                $repo = $urbanistesRepository;
                $cible = "Urbanistes";
                break;
            case 'lotisseur' :
                $repo = $lotisseursRepository;
                $cible = "Lotisseurs";
                break;
            case 'expertAssu' :
                $repo = $expertAssuRepository;
                $cible = "Experts en assurances";
                break;
           case 'mairie' :
               $repo = $villeMailingRepository;
               $cible="Mairie";
               break;
        }
        $propects = $repo->findBy(['desabonner'=>false]);

        if ($content->code ===""){
            $code =null;
        }
        else{
            $code = $content->code;
        }
        $campagne = new CampagneMail();
        $campagne->setCible($cible)
            ->setDate(new \DateTime())
            ->setDescription($content->description)
            ->setTitreEmail($content->titre)
            ->setCodePromo($code)
            ->setType('autre')
            ->setContenu($content->content)
            ->setMailEnvoye(count($propects));
        $this->manager->persist($campagne);
        $this->manager->flush();
        $liste = ['coquard.dominique@gmail.com','contact@diag-drone.com','marinvincent84000@hotmail.fr',"mathieumiranda@hotmail.com","axel.cloux@gmail.com"];
        $i=0;

        foreach ($propects as $propect){
            if ($departements){
                foreach ($departements as $departement){
                    if ($propect->getMail()){
                        if (($propect->getCodePostal()>= (int)$departement *1000)&&($propect->getCodePostal()<=(int)$departement*1000+999)){

                           //$mail->mailLancementAutre($propect->getMail(),$content->titre,$content->content,$code,$propect->getId(),$content->cible);
                            $i++;

                        }
                    }
                }
            }
            else{
                $i++;
                $mail->mailLancementAutre($propect->getMail(),$content->titre,$content->content,$code,$propect->getId(),$content->cible);
            }
        }

       for ($i=0, $iMax = count($liste); $i< $iMax; $i++){
            $mail->mailLancementAutre($liste[$i],$content->titre,$content->content,$code,0,$content->cible);
        }

        return new JsonResponse();

    }

    /**
     * @param $type
     * @param $id
     * @param ContactBtpRepository $contactBtpRepository
     * @param AdminBienRepository $adminBienRepository
     * @param AgentImmoRepository $agentImmoRepository
     * @param BureauEtudeRepository $bureauEtudeRepository
     * @param ControleBatRepository $controleBatRepository
     * @param EntrepriseTpRepository $entrepriseTpRepository
     * @param ExpertAssuRepository $expertAssuRepository
     * @param GeometresExpertsRepository $geometresExpertsRepository
     * @param MaitresDOeuvreEnBatimentRepository $oeuvreEnBatimentRepository
     * @param OfficesEtGestionDHlmRepository $officesEtGestionDHlmRepository
     * @param PromoteursConstructeursRepository $promoteursConstructeursRepository
     * @param UrbanistesRepository $urbanistesRepository
     * @param LotisseursRepository $lotisseursRepository
     * @param $repo
     * @param AgentAssuRepository $agentAssuRepository
     * @Route("/seDesabonnerAutre/{type}-{id}",name="seDesabonneAutre")
     */
    public function seDesabonnerAutre($type,$id,ContactBtpRepository           $contactBtpRepository, AdminBienRepository $adminBienRepository, AgentImmoRepository $agentImmoRepository,
                                      BureauEtudeRepository          $bureauEtudeRepository, ControleBatRepository $controleBatRepository, EntrepriseTpRepository $entrepriseTpRepository,
                                      ExpertAssuRepository           $expertAssuRepository, GeometresExpertsRepository $geometresExpertsRepository, MaitresDOeuvreEnBatimentRepository $oeuvreEnBatimentRepository,
                                      OfficesEtGestionDHlmRepository $officesEtGestionDHlmRepository, PromoteursConstructeursRepository $promoteursConstructeursRepository, UrbanistesRepository $urbanistesRepository,
                                      VilleMailingRepository $villeMailingRepository,
                                      LotisseursRepository           $lotisseursRepository,AgentAssuRepository $agentAssuRepository, $repo=null){
        switch ($type){
            case 'abien' :
                $repo = $adminBienRepository;

                break;
            case 'agentAssu' :
                $repo = $agentAssuRepository;
                break;
            case 'agentImmo' :
                $repo = $agentImmoRepository;
                break;
            case 'bureauEtude' :
                $repo = $bureauEtudeRepository;
                break;
            case 'btp' :
                $repo = $contactBtpRepository;
                break;
            case 'controlBtp' :
                $repo = $controleBatRepository;
                break;
            case 'tp' :
                $repo = $entrepriseTpRepository;
                break;
            case 'geometre' :
                $repo = $geometresExpertsRepository;
                break;
            case 'maitre' :
                $repo = $oeuvreEnBatimentRepository;
                break;
            case 'hlm' :
                $repo = $officesEtGestionDHlmRepository;
                break;
            case 'promoteur' :
                $repo = $promoteursConstructeursRepository;
                break;
            case 'urbaniste' :
                $repo = $urbanistesRepository;
                break;
            case 'lotisseur' :
                $repo = $lotisseursRepository;
                break;
            case 'expertAssu' :
                $repo = $expertAssuRepository;
                break;
            case 'mairie' :
                $repo = $villeMailingRepository;
                break;
        }

        $prospect = $repo->findOneBy(['id'=>$id]);
        $prospect->setDesabonner(true);
        $this->manager->persist($prospect);
        $this->manager->flush();

        return $this->render("accueil/desobonner.html.twig");
    }

    /**
     * @return Response
     * @Route("/administrateur/mailuniqueAmbassadeur",name="mailAmbassadeurUnique")
     */
    public function mailAmbassadeur():Response{
        return $this->render('administrateur/mailAmbassadeur.html.twig');
    }

    /**
     * @param Mail $mail
     * @return JsonResponse
     * @Route("/administrateur/envoieMailAmbassadeur")
     */
    public function envoieMailAmbassadeur(Mail $mail):JsonResponse{
        $adresseEmail = $this->request->getContent();
        $mail->mailuniqueAmba($adresseEmail);

        return new JsonResponse();
    }

    /**
     * @param Mail $mail
     * @return Response
     * @Route("/administrateur/mailTest",name="mailtest")
     */
    public function mailTest(Mail $mail){
        for ($i = 0; $i <= 5000; $i++) {
            try {
                $mail->mailTest("mathieumiranda@hotmail.com");
            }
            catch (\Exception $e){
                
                fwrite($fp, serialize($e));
                fclose($fp);
            }

        }
         return $this->render('test.html.twig');
    }

    /**
     * @return Response
     * @Route ("/administrateur/journaliste",name="journaliste")
     */
    public function journaliste(){

        return $this->render('administrateur/journaliste.html.twig');
    }

    /**
     * @param Mail $mail
     * @param JournalisteRepository $repository
     * @return JsonResponse
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @Route("/administrateur/mailJournaliste")
     */
    public function mailJournaliste(Mail $mail,JournalisteRepository $repository):JsonResponse{
        $content = json_decode($this->request->getContent());


        $journalistes = $repository->findBy(['desabonner'=>false]);
       // $mail->mailLancement("mathieumiranda@hotmail.com",$content->titre,$content->para);
        $test = [];
        foreach ($journalistes as  $journaliste){

                if ($journaliste->getMail()){
                    //$mail->mailLancement($journaliste->getMail(),$content->titre,$content->para);
                    $test[]= new Address($journaliste->getMail());
                }
        }
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @param JournalisteRepository $repository
     * @return Response
     * @Route("/accueil/sedesabonne-jounraliste/{id}",name="desabonnerJournaliste")
     */
    public function deDesbonnerJournaliste(int $id,JournalisteRepository $repository){
        $journaiste = $repository->findOneBy(['id'=>$id]);
        $journaiste->setDesabonner(true);
        $this->manager->persist($journaiste);
        $this->manager->flush();

        return $this->render('accueil/seDesabonner.htm.twig');
    }

    /**
     * @return Response
     * @Route("/administrateur/tableForStat",name="tableForStat")
     */
    public function tableForStat(){

        return $this->render('administrateur/statmail.html.twig');
    }

    /**
     * @param ContactBtpRepository $contactBtpRepository
     * @param AdminBienRepository $adminBienRepository
     * @param AgentImmoRepository $agentImmoRepository
     * @param BureauEtudeRepository $bureauEtudeRepository
     * @param ControleBatRepository $controleBatRepository
     * @param EntrepriseTpRepository $entrepriseTpRepository
     * @param ExpertAssuRepository $expertAssuRepository
     * @param GeometresExpertsRepository $geometresExpertsRepository
     * @param MaitresDOeuvreEnBatimentRepository $oeuvreEnBatimentRepository
     * @param OfficesEtGestionDHlmRepository $officesEtGestionDHlmRepository
     * @param PromoteursConstructeursRepository $promoteursConstructeursRepository
     * @param UrbanistesRepository $urbanistesRepository
     * @param LotisseursRepository $lotisseursRepository
     * @param AgentAssuRepository $agentAssuRepository
     * @param Mail $mail
     * @param EquipeRepository $equipeRepository
     * @param VilleMailingRepository $villeMailingRepository
     * @param string $type
     * @return StreamedResponse
     * @Route ("/administrateur/export/{type}",name="export")
     */
    public function export(ContactBtpRepository           $contactBtpRepository, AdminBienRepository $adminBienRepository, AgentImmoRepository $agentImmoRepository,
                           BureauEtudeRepository          $bureauEtudeRepository, ControleBatRepository $controleBatRepository, EntrepriseTpRepository $entrepriseTpRepository,
                           ExpertAssuRepository           $expertAssuRepository, GeometresExpertsRepository $geometresExpertsRepository, MaitresDOeuvreEnBatimentRepository $oeuvreEnBatimentRepository,
                           OfficesEtGestionDHlmRepository $officesEtGestionDHlmRepository, PromoteursConstructeursRepository $promoteursConstructeursRepository, UrbanistesRepository $urbanistesRepository,
                           LotisseursRepository           $lotisseursRepository,AgentAssuRepository $agentAssuRepository,Mail $mail,EquipeRepository $equipeRepository,VilleMailingRepository $villeMailingRepository,string $type): StreamedResponse
    {
        $repo=null;
        $cible=null;
        switch ($type){
            case 'abien' :
                $repo = $adminBienRepository;
                $cible = "Administrateur de bien et syndic";
                break;
            case 'equipe' :
                $repo = $equipeRepository;
                $cible = "Mail test";
                break;
            case 'agentAssu' :
                $repo = $agentAssuRepository;
                $cible = "Agents d'assurances";
                break;
            case 'agentImmo' :
                $repo = $agentImmoRepository;
                $cible = "Agents immobilier";
                break;
            case 'bureauEtude' :
                $repo = $bureauEtudeRepository;
                $cible = "Bureau d'étude";
                break;
            case 'btp' :
                $repo = $contactBtpRepository;
                $cible = "BTP";
                break;
            case 'controlBtp' :
                $repo = $controleBatRepository;
                $cible = "Controlleurs du bâtiments";
                break;
            case 'tp' :
                $repo = $entrepriseTpRepository;
                $cible = "Entreprise travaux public";
                break;
            case 'geometre' :
                $repo = $geometresExpertsRepository;
                $cible = "Géomètres";
                break;
            case 'maitre' :
                $repo = $oeuvreEnBatimentRepository;
                $cible = "Maitre d'oeuvre";
                break;
            case 'hlm' :
                $repo = $officesEtGestionDHlmRepository;
                $cible = "Office de gestion HLM";
                break;
            case 'promoteur' :
                $repo = $promoteursConstructeursRepository;
                $cible = "Promoteurs et constructeurs";
                break;
            case 'urbaniste' :
                $repo = $urbanistesRepository;
                $cible = "Urbanistes";
                break;
            case 'lotisseur' :
                $repo = $lotisseursRepository;
                $cible = "Lotisseurs";
                break;
            case 'expertAssu' :
                $repo = $expertAssuRepository;
                $cible = "Experts en assurances";
                break;
            case 'mairie' :
                $repo = $villeMailingRepository;
                $cible="Mairie";
                break;
        }
        $fileName = $type.date('d-m-y') . ".csv";
        $clients = $repo->findBy(['desabonner'=>false]);
        $response = new StreamedResponse();
        $response->setCallback(function() use ($clients,$type){
            $handle = fopen('php://output', 'wb+');

            // Nom des colonnes du CSV
            fputcsv($handle, array(
                'nom',
                'Code Postal',
                'ville',
                'mail'
            ), ';');

            //Champs

            foreach ($clients as $index => $client)
            {

                //dump($client);die();
                if ($type ==="mairie"){
                    fputcsv($handle,array(
                        $client->getNom(),
                        $client->getCodePostal(),
                        null,
                        $client->getMail()
                    ),';');
                }
                else{
                    fputcsv($handle,array(
                        $client->getNom(),
                        $client->getCodePostal(),
                        $client->getVille(),
                        $client->getMail()
                    ),';');
                }

            }
            fclose($handle);
        });


        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename='.$fileName);


        return $response;
    }
}