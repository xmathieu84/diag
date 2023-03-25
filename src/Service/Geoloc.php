<?php


namespace App\Service;

class   Geoloc
{

    /**
     * Géolocalisation selon l'adresse
     *
     * @param object $inscrit
     * @return array
     */
    public function geolocalisation($inscrit): array
    {
        $adresse = $inscrit->getAdresse()->getNumero()
            . '+' . str_replace(' ','+',$inscrit->getAdresse()->getNomVoie())
            . '+' . $inscrit->getAdresse()->getCodePostal();
        $geocoder  = "https://api-adresse.data.gouv.fr/search/?q=$adresse";

        $query = sprintf($geocoder, urlencode($adresse));
        $result = json_decode(file_get_contents($query));
        $json = $result->features[0];
        $lat = $json->geometry->coordinates[1];
        $lon = $json->geometry->coordinates[0];


        return [$lat, $lon];
    }

    /**
     * @param string $adresse
     * @return array
     */
    public function localise(string $adresse):array{
        $geocoder  = "https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false,+CA&key=AIzaSyB7MwwRvn0FLxtVv6GRuUEo3QDgPDMQkWU";
        $query = sprintf($geocoder,urlencode($adresse));
        $result = json_decode(file_get_contents($query));
        $lat = $result->results[0]->geometry->location->lat;
        $lon = $result->results[0]->geometry->location->lng;

        return [$lat,$lon];
    }

    /**
     * Definis les coordonnées de deplacement maximal d'un OTD
     *
     * @param float $lat
     * @param float $lon
     * @param int $distanceInter
     * @return array
     */
    public function distance(float $lat, float $lon, int $distanceInter): array
    {

        $minlat = $lat - $distanceInter / (111 * cos(deg2rad($lat)));
        $maxlat = $lat + $distanceInter / (111 * cos(deg2rad($lat)));
        $minlon = $lon - $distanceInter / (111 * cos(deg2rad($lon)));
        $maxlon = $lon + $distanceInter / (111 * cos(deg2rad($lon)));
        return [$minlat, $maxlat, $minlon, $maxlon];
    }

    /**
     * Defini le temps de trajet d'un OTD vers sa mission
     *
     * @param float $latorigine
     * @param float $lonorigine
     * @param float $latdestination
     * @param float $londestination
     * @return int
     */
    public function tempsTrajet(float $latorigine, float $lonorigine, float $latdestination, float $londestination): int
    {
        $duration = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=%s,%s&destinations=%s,%s&mode=driving&language=fr-FR&key=AIzaSyB7MwwRvn0FLxtVv6GRuUEo3QDgPDMQkWU';
        $query = sprintf($duration, urlencode(utf8_encode($latorigine)), urlencode(utf8_encode($lonorigine)), urlencode(utf8_encode($latdestination)), urlencode(utf8_encode($londestination)));
        $result = json_decode(file_get_contents($query));
        $duree = $result->rows[0]->elements[0]->duration->value;
        return $duree;
    }

    /**
     * Undocumented function
     *
     * @param string|null $numero
     * @param string $nomVoie
     * @param int $cp
     * @param string $ville
     * @return array
     */
    public function geoMeteo(?string $numero, string $nomVoie, int $cp, string $ville): array
    {
        $geocoder  = "https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false,+CA&key=AIzaSyB7MwwRvn0FLxtVv6GRuUEo3QDgPDMQkWU";
        $adresse = $numero . ',' . $nomVoie . ',' . $cp . ',' . $ville;
        $query = sprintf($geocoder, urlencode($adresse));
        $result = json_decode(file_get_contents($query));
        $json = $result->results[0];
        $lat = $json->geometry->location->lat;
        $lon = $json->geometry->location->lng;
        return [$lat, $lon];
    }

    /**
     * distance pour le calcul du cout kilometrique
     *
     * @param float $latorigine
     * @param float $lonorigine
     * @param float $latdestination
     * @param float $londestination
     * @return float
     */
    public function coutKilometre(float $latorigine, float $lonorigine, float $latdestination, float $londestination): float
    {
        $duration = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=%s,%s&destinations=%s,%s&mode=driving&language=fr-FR&key=AIzaSyB7MwwRvn0FLxtVv6GRuUEo3QDgPDMQkWU';
        $query = sprintf($duration, urlencode(utf8_encode($latorigine)), urlencode(utf8_encode($lonorigine)), urlencode(utf8_encode($latdestination)), urlencode(utf8_encode($londestination)));
        $result = json_decode(file_get_contents($query));
        $longueurTrajet = $result->rows[0]->elements[0]->distance->value;
        return $longueurTrajet;
    }

    /**
     * @param string $nomCommune
     * @return mixed
     */
    public function departement(string $nomCommune)
    {
        $adresse = 'https://geo.api.gouv.fr/communes?nom=%s&fields=&format=json&geometry=centre';
        $query = sprintf($adresse, urlencode($nomCommune));
        $result = json_decode(file_get_contents($query), true);
        return $result[0]['codeDepartement'];
    }

    /**
     * @param float $latInter
     * @param float $lonInter
     * @param float $latOtd
     * @param float $lonOtd
     * @return int
     */
    public function distanceKm(float $latInter, float $lonInter, float $latOtd, float $lonOtd): float
    {
        $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon
        $rlo1 = deg2rad($lonInter);
        $rla1 = deg2rad($latInter);
        $rlo2 = deg2rad($lonOtd);
        $rla2 = deg2rad($latOtd);
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin(
            $dlo
        ));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = round($earth_radius * $d / 1000, 2);
        return $distance;
    }

    public function notamAdresse($adresse){
        $geocoder  = "https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false,+CA&key=AIzaSyB7MwwRvn0FLxtVv6GRuUEo3QDgPDMQkWU";

        $query = sprintf($geocoder, urlencode($adresse));
        $result = json_decode(file_get_contents($query), true, 512, JSON_THROW_ON_ERROR);
        $json = $result['results'][0];
        $lat = $json['geometry']['location']['lat'];
        $lon = $json['geometry']['location']['lng'];

        return [$lat,$lon];
    }


}
