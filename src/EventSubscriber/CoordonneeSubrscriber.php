<?php


namespace App\EventSubscriber;


use App\Entity\Coordonnees;
use App\Entity\Demandeur;
use App\Event\AdressEvent;

use App\Repository\MailPrefectureRepository;
use App\Service\DefinirObjet;
use App\Service\Geoloc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class CoordonneeSubrscriber
 * @package App\EventSubscriber
 */
class CoordonneeSubrscriber implements EventSubscriberInterface
{


    /**
     * @var Geoloc
     */
    protected $geoloc;

    /**
     * @var DefinirObjet
     */
    protected $definirObjet;

    protected MailPrefectureRepository $repository;

    /**
     * CoordonneeSubrscriber constructor.
     * @param Geoloc $geoloc
     * @param DefinirObjet $definirObjet
     * @param MailPrefectureRepository $repository
     */
    public function __construct(Geoloc $geoloc,DefinirObjet $definirObjet,MailPrefectureRepository $repository)
    {
        $this->geoloc = $geoloc;
        $this->definirObjet=$definirObjet;
        $this->repository=$repository;

    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents():array
    {
        return [
            AdressEvent::NAME=>'createCoordonnne'
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function createCoordonnne(GenericEvent $event)
    {
        $utilisateur = $event->getSubject();
        $coordonnee = new Coordonnees();
        $localisation = $this->geoloc->geolocalisation($utilisateur);
        $cp = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$utilisateur->getAdresse()->getCodePostal()));
        $departement = $this->repository->findOneBy(['numeroDepartement'=>$cp[0]->codeDepartement]);

         $this->definirObjet->definirCoordonnee(
            $coordonnee,
            $utilisateur->getAdresse(),
            $localisation[0],
            $localisation[1],
            null,
            null,
            null,
            null
        );
         $utilisateur->getAdresse()->setDepartement($departement);


    }
}