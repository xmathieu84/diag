<?php

namespace App\Service;



use App\Entity\Adresse;
use App\Entity\Civilite;
use App\Entity\Coordonnees;
use App\Entity\Entreprise;
use App\Entity\Salarie;
use App\Entity\Telephone;
use App\Helper\EntityManagerTrait;

class DefinirObjet
{
    use EntityManagerTrait;

    /**
     * @param Coordonnees $coordonnee
     * @param Adresse $adresse
     * @param float $latitude
     * @param float $longitude
     * @param float|null $latMax
     * @param float|null $latMin
     * @param float|null $lonMax
     * @param float|null $lonMin
     */
    public function definirCoordonnee(Coordonnees $coordonnee,Adresse $adresse,
                                      float $latitude, float $longitude, ?float $latMax,?float $latMin,?float $lonMax,?float $lonMin)
    {

        $coordonnee->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setLatMaxInter($latMax)
            ->setLatMinInter($latMin)
            ->setLonMaxInter($lonMax)
            ->setLonMinInter($lonMin);
        $adresse->setCoordonnees($coordonnee);

        $this->manager->persist($coordonnee);
    }


    /**
     * @param Adresse $adresse
     * @param string|null $numero
     * @param string $nomVoie
     * @param int $codePostal
     * @param string $ville
     * @return Adresse
     */
    public function definirAdresse(Adresse $adresse, ?string $numero, string $nomVoie, int $codePostal,string $ville)
    {

        $adresse->setNumero($numero)
            ->setNomVoie($nomVoie)
            ->setCodePostal($codePostal)
            ->setVille($ville);
        return $adresse;
    }

    /**
     * @param Civilite $civilite
     * @param string $type (Monsieur ou Madame)
     * @param string $nom
     * @param string $prenom
     * @return Civilite
     */
    public function definirCivilite(Civilite $civilite,string $type, string $nom, string $prenom):Civilite
    {
        $civilite->setType($type)
            ->setNom($nom)
            ->setPrenom($prenom);


        return $civilite;
    }

    /**
     * Undocumented function
     *
     * @param Salarie $salarie
     * @param Entreprise $entreprise
     * @param Telephone $telephone
     * @param string $validation
     * @param Adresse $adresse
     * @param Civilite $civilite
     * @return Salarie
     */
    public function definirSalarie(Salarie $salarie,Entreprise $entreprise,Telephone $telephone, string $validation,Adresse $adresse,Civilite $civilite):Salarie
    {
        $salarie->setEntreprise($entreprise)
            ->setTelephone($telephone)
            ->setValidation($validation)
            ->setAdresse($adresse)
            ->setCivilite($civilite);
        return $salarie;
    }

    /**
     * @param $objet
     * @return array
     */
    public function obtenirCordonnee($objet)
    {
        $latitude = $objet->getAdresse()->getCoordonnees()->getLatitude();
        $longitude = $objet->getAdresse()->getCoordonnees()->getLongitude();
        return [$latitude, $longitude];
    }
}
