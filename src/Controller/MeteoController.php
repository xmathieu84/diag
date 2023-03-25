<?php

namespace App\Controller;

use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;

use App\Service\DefinirDate;
use App\Service\Geoloc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;

class MeteoController extends AbstractController
{
    use RequestTrait,EntityManagerTrait,InterRepoTrait;
    /**
     * @Route("/meteo", name="meteo")
     */
    public function recupererMeteo(Geoloc $geoloc,DefinirDate $definirDate)
    {
        $contenu = $this->request->getContent();
        $adresse  =json_decode($contenu,true);
        $maintenant = $definirDate->aujourdhui();
        $intervention = $this->interventionRepository->findOneBy(['id'=>$adresse['inter']]);
        $interRdv = $intervention->getRdvAt();
        $interval = $interRdv->diff($maintenant);
        $nombreJours = $interval->format('%d');
        $moment = $interRdv->format('h');
        
        $localisation = $geoloc->geoMeteo($adresse['numeroVoie'],$adresse['nomVoie'],$adresse['codePostal'],$adresse['nomVille']);
        $gps = $localisation[0].','. $localisation[1];

        $data = file_get_contents('https://api.meteo-concept.com/api/forecast/daily/'.$nombreJours.'/periods?token=54ebb9b32aa2865b80cf554753341e5622c1632b4841ed4c5efc3528532cb85d&latlng='.$gps);;
        
        $meteo = json_decode($data);

        if ($moment<12) {
            $meteoMoment = $meteo->forecast[1];
        }
        else {
            $meteoMoment = $meteo->forecast[2];
        }
        $reponse = new JsonResponse();

        $reponse->setData([
            'ventRafale'=>$meteoMoment->gust10m,
            'vent'=>$meteoMoment->wind10m,
            'pluie'=>$meteoMoment->probarain,
            'temperature'=>$meteoMoment->temp2m
        ]);
        return $reponse;
        

    }
}
