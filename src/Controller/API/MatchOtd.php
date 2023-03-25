<?php


namespace App\Controller\API;


use App\Entity\AccueilMatchOtd;
use App\Helper\SalarieRepoTrait;
use App\Service\Geoloc;
use Symfony\Component\HttpFoundation\JsonResponse;

class MatchOtd
{
    use SalarieRepoTrait;
    /**
     * @var Geoloc
     */
    private Geoloc $geoloc;
    public function __construct(Geoloc $geoloc)
    {
        $this->geoloc=$geoloc;
    }

    public function __invoke(AccueilMatchOtd $data):JsonResponse
    {
        $adresse = str_replace(' ',',',$data->getAdresse());
        $date = (new \DateTime)->setTimestamp($data->getDateinter()/1000);

        $coordonnee = $this->geoloc->localise($adresse);
        $salaries = $this->salarieRepository->nombreSalarieAccueil($data->getIdListeInter(),$data->getIdlisteInter(),$date,$coordonnee[0],$coordonnee[1]);
        $response = new JsonResponse();
        return $response->setData(count($salaries));
    }
}