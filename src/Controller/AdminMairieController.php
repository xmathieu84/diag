<?php

namespace App\Controller;

use App\Entity\ComuComMailing;

use App\Entity\CoorOtd;
use App\Entity\VilleMailing;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\ComuComMailingRepository;
use App\Repository\FichierOTDRepository;
use App\Repository\MailPrefectureRepository;
use App\Repository\VilleMailingRepository;
use App\Service\Geoloc;
use App\Service\Xml;
use ExtPHP\XmlToJson\XmlToJsonConverter;
use SimpleXMLIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMairieController extends AbstractController
{
    use RequestTrait, EntityManagerTrait;
    /**
     * @Route("/mairie", name="admin_mairie")
     */
    public function index(MailPrefectureRepository $mailPrefectureRepository): Response
    {
        $mails = $mailPrefectureRepository->findAll();
        return $this->render('administrateur/listeMairie.html.twig', []);
    }

    /**
     * @Route("/admin/listeVille",name="listeVille")
     *
     * @param VilleMailingRepository $repository
     * @return Response
     */
    public function recupererMairie(VilleMailingRepository $repository):Response
    {

        $villes = $repository->findAll();



        return $this->render('administrateur/listeVille.html.twig',[
            'villes'=>$villes
        ]);
    }
    /**
     * @Route("/admin/commu")
     */
    public function listeComuCom(ComuComMailingRepository $repository){
        $coms = $repository->findAll();
        $test = simplexml_load_string(file_get_contents('../public/uploads/xml/carte.xml'));
        $converter = new XmlToJsonConverter($test);
        $vue = $converter->toJson();
       


         return $this->render('administrateur/listeComm.html.twig',['coms'=>$coms]);
    }

    /**
     * @param FichierOTDRepository $repository
     * @param Geoloc $geoloc
     * @return Response
     * @Route ("/coord")
     */
    public function loadCoord(FichierOTDRepository $repository,Geoloc $geoloc){
        $otds = $repository->findAll();

        foreach ($otds as $otd){
        $adresse = 'https://api-adresse.data.gouv.fr/search/?q='.str_replace(' ','+',$otd->getVille());
            try {
                $result = json_decode(file_get_contents($adresse));


                $coord = new CoorOtd();

                $coord->setLat($result->features[0]->geometry->coordinates[1])
                    ->setLon($result->features[0]->geometry->coordinates[0])
                    ->setOtd($otd);
                $this->manager->persist($coord);
                $this->manager->flush();
            }
            catch (\Exception $e){
                
            }

        }

        return $this->render('test.html.twig');

    }

    /**
     *
     * @return Response
     * @Route("/test")
     *
     */
    public function testXml(){
        ini_set('memory_limit', '2000M');
        $fichier  = file_get_contents('../public/uploads/xml/carte.xml');
        $xmlIterator = new SimpleXMLIterator( $fichier );
        $test = new SimpleXMLIterator($xmlIterator->PartieS);
        return $this->render('test.html.twig');
    }

    /**
     * @return JsonResponse
     * @Route("/json")
     */
    public function Tojson(){

        $fichier  = simplexml_load_string(file_get_contents('../public/uploads/xml/carte.xml'));

        return new JsonResponse($fichier);
    }
}
