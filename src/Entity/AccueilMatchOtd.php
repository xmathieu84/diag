<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\API\MatchOtd;

/**
 * Class AccueilMatchOtd
 * @package App\Entity
 * @ApiResource (collectionOperations={
*           "matchOtd"={
*               "method":"POST",
 *              "path":"/matchOtd",
 *              "controller":MatchOtd::class
 *     }
 *     },itemOperations={},
 *
 *     )
 *
 */
class AccueilMatchOtd
{
    public $id;
    private $adresse;
    private $idTypeInter;
    private $idListeInter;
    private $dateinter;

    /**
     * @return mixed
     */
    public function getIdTypeInter():int
    {
        return $this->idTypeInter;
    }

    /**
     * @param mixed $idTypeInter
     */
    public function setIdTypeInter($idTypeInter): void
    {
        $this->idTypeInter = $idTypeInter;
    }


    /**
     * @return mixed
     */
    public function getAdresse():string
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse): void
    {
        $this->adresse = $adresse;
    }



    /**
     * @return mixed
     */
    public function getIdListeInter():int
    {
        return $this->idListeInter;
    }

    /**
     * @param mixed $idListeInter
     */
    public function setIdListeInter($idListeInter): void
    {
        $this->idListeInter = $idListeInter;
    }

    /**
     * @return mixed
     */
    public function getDateinter():int
    {
        return $this->dateinter;
    }

    /**
     * @param mixed $dateinter
     */
    public function setDateinter($dateinter): void
    {
        $this->dateinter = $dateinter;
    }

}