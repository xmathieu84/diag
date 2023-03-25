<?php


namespace App\Controller;
use App\Helper\InterRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\TempsSensibleRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Geoloc;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class AssictanceVolController extends AbstractController
{
    use InterRepoTrait,RequestTrait;

    /**
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/assistance au vol",name="salListeInter")
     * @Security ("is_granted('ROLE_SALARIE') and is_granted('ROLE_CLASSIC')")
     */
    public function listeInterSalarie(choixTemplate $choixTemplate):Response{

        $template = $choixTemplate->templateSal($this->getUser());

        $inter = $this->interventionRepository->findBySalarie($template[0]);

        return $this->render('salarie/listeInter.html.twig',[
            'template'=>$template[1],
            'interventions'=>$inter
        ]);

    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/coordInter/{id}")
     *  @Security ("is_granted('ROLE_SALARIE') and is_granted('ROLE_CLASSIC')")
     */
    public function coorInter(int $id){
        $inter = $this->interventionRepository->findOneBy(['id'=>$id]);
        $lat = $inter->getAdresse()->getCoordonnees()->getLatitude();
        $lon = $inter->getAdresse()->getCoordonnees()->getLongitude();
        $R = 30;
        $response = new JsonResponse();
        return $response->setData([
            'lat'=>$lat,
            'lon'=>$lon,
            'rayon'=>100
        ]);
    }

    /**
     * @param int $id
     * @param DefinirDate $definirDate
     * @return JsonResponse
     * @throws Exception
     * @Route("/meteoInter/{id}")
     *  @Security ("is_granted('ROLE_SALARIE') and is_granted('ROLE_CLASSIC')")
     */
    public function meteoInter(int $id,DefinirDate $definirDate){
        $inter = $this->interventionRepository->findOneBy(['id'=>$id]);
        $lat = $inter->getAdresse()->getCoordonnees()->getLatitude();
        $lon = $inter->getAdresse()->getCoordonnees()->getLongitude();
        $gps = $lat.','.$lon;
        $date = $definirDate->aujourdhui();
        $interRdv = $inter->getRdvAt();
        $interval = $interRdv->diff($date);
        $nombreJours = $interval->format('%d');
        $data = file_get_contents('https://api.meteo-concept.com/api/forecast/daily/'.$nombreJours.'/periods?token=54ebb9b32aa2865b80cf554753341e5622c1632b4841ed4c5efc3528532cb85d&latlng='.$gps);
        $meteo = json_decode($data);

        $moment = $interRdv->format('h');
        if ($moment<=12) {
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
        return  $reponse;
    }

    /**
     * @param choixTemplate $choixTemplate
     * @param DefinirDate $definirDate
     * @return Response
     * @throws Exception
     * @Route("/notam",name="notam")
     * @Security ("is_granted('ROLE_SALARIE') and is_granted('ROLE_CLASSIC')")
     */
    public function notam(choixTemplate $choixTemplate,DefinirDate $definirDate):Response{
        $template = $choixTemplate->templateSal($this->getUser());
        $date = $definirDate->aujourdhui();


        return $this->render('salarie/notam.html.twig',[
            'template'=>$template[1],
            'date'=>$date
        ]);
    }

    /**
     * @param Geoloc $geoloc
     * @return JsonResponse
     * @Route ("/listeNotam")
     * @Security ("is_granted('ROLE_SALARIE') and is_granted('ROLE_CLASSIC')")
     */
    public function listeNotam(Geoloc $geoloc): JsonResponse
    {

        ini_set('memory_limit', '2000M');
        ini_set(' max_execution_time',0);

        $contenu = $this->request->getContent();
        $coordonnees=$geoloc->notamAdresse($contenu);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.laminardata.aero/v2/notams?user_key=7b68cba351c9abcd335794ce6eb260ae',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                      "type": "Feature",
                      "geometry": {
                        "type": "Point",
                        "coordinates": [
                          '.$coordonnees[1].','.
                          $coordonnees[0].'
                        ]
                      }
                    }
                    ',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/geo+json'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $notam = json_decode($response);


        $fichier  = simplexml_load_string(file_get_contents('../public/uploads/xml/carte.xml'));

        $interdit = [];
        foreach ($fichier->Situation->PartieS as $party){
            foreach ($party as $item){
            $interdit[] = $item;
                
            }
        }

        $response = new JsonResponse();
        return $response->setData(['coord'=>$coordonnees,'zone'=>$fichier,'notam'=>$notam->features]);
    }

    /**
     * @return Response
     * @Route ("/assistance au vol/meteo",name="meteoInter")
     */
    public function listeInterMeteo(DefinirDate $definirDate){
        $entreprise = $this->getUser()->getSalarie()->getEntreprise();
        $date = $definirDate->aujourdhui();
        $interventions = $this->interventionRepository->findForMeteoInter($entreprise,$date);

        return $this->render('entreprise/listeInterMeteo.html.twig',[
            'interventions'=>$interventions
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route ("/retournMeteo/{id}")
     */
    public function retourMeteoInter($id,DefinirDate $definirDate,TempsSensibleRepository $repository):JsonResponse{
        $intervention = $this->interventionRepository->findOneBy(['id'=>$id]);
        $retourTemps = [];
        $coordonnees = $intervention->getAdresse()->getCoordonnees();
        $maintenant = $definirDate->aujourdhui()->format('d/m/Y');
        $rdv = $intervention->getRdvAT()->format('d/m/Y');
        $heure = $intervention->getRdvAT()->format('h');
        $maintenantNet = \DateTime::createFromFormat('d/m/Y H:i:s',$maintenant.' 00:00:00');
        $rdvNet = \DateTime::createFromFormat('d/m/Y H:i:s',$rdv.' 00:00:00');
        $interval = $rdvNet->diff($maintenantNet)->format('%d');
        $data = file_get_contents('https://api.meteo-concept.com/api/forecast/daily/periods?token=54ebb9b32aa2865b80cf554753341e5622c1632b4841ed4c5efc3528532cb85d&latlng='.$coordonnees->getLatitude().','.$coordonnees->getLongitude());
        $dataFinal = json_decode($data);
        if ($heure<12){
            $tempsGlobal = $dataFinal->forecast[$interval][1];
        }
        else{
            $tempsGlobal = $dataFinal->forecast[$interval][1];
        }
        $typeTemps = $repository->findOneBy(['code'=>$tempsGlobal->weather]);
        $retourTemps[]=["temps"=>$typeTemps->getDescription(),'temp'=>$tempsGlobal->temp2m,'vent'=>$tempsGlobal->wind10m,'rafale'=>$tempsGlobal->gust10m];
        return new JsonResponse($retourTemps);
    }
}