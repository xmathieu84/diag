<?php


namespace App\EventSubscriber;


use App\Entity\Proposition;
use App\Event\ApiMatchSalarieEvent;
use App\Repository\FichierOTDRepository;
use App\Repository\SalarieRepository;
use App\Service\DefinirDate;
use App\Service\Geoloc;
use App\Service\Mail;
use App\Service\SmsFactor;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\GenericEvent;

class MatchOtdInterventionApi implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{


    /**
     * @var DefinirDate
     */
    protected DefinirDate $definirDate;
    /**
     * @var Mail
     */
    protected Mail $mail;

    protected SalarieRepository $salarieRepository;

    protected EntityManagerInterface $manager;

    protected FichierOTDRepository $fichierOTDRepository;

    protected Geoloc $geoloc;
    protected SmsFactor $smsFactor;

    /**
     * MatchOtdInterventionApi constructor.
     * @param DefinirDate $definirDate
     * @param Mail $mail
     * @param SalarieRepository $salarieRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(DefinirDate $definirDate,Mail $mail,SalarieRepository $salarieRepository,
                                EntityManagerInterface $manager,FichierOTDRepository $fichierOTDRepository,Geoloc $geoloc,SmsFactor $smsFactor)
    {
        $this->definirDate = $definirDate;
        $this->mail=$mail;
        $this->manager = $manager;
        $this->salarieRepository=$salarieRepository;
        $this->fichierOTDRepository = $fichierOTDRepository;
        $this->geoloc = $geoloc;
        $this->smsFactor = $smsFactor;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents():array
    {
        return [ApiMatchSalarieEvent::MATCH=>'createProposition'];
    }

    /**
     * @param GenericEvent $event
     * @throws Exception
     */
    public function createProposition(GenericEvent $event){
        $intervention = $event->getSubject();
        $limite = 10;
        $jour = $this->definirDate->aujourdhui();
        if ($intervention->getDateWitch()){
            $dateInter = \DateTime::createFromFormat("d/m/Y H:i:s",$intervention->getDateWitch()->format('d/m/Y').' 00:00:00');
            $dateJour = \DateTime::createFromFormat("d/m/Y H:i:s",$jour->format('d/m/Y').' 00:00:00');
        }
        elseif ($intervention->getDateDebut()){
            $dateInter = \DateTime::createFromFormat("d/m/Y H:i:s",$intervention->getDateDebut()->format('d/m/Y').' 00:00:00');
            $dateJour = \DateTime::createFromFormat("d/m/Y H:i:s",$jour->format('d/m/Y').' 00:00:00');
        }
            $salaries= $this->salarieRepository->findOtdFinal($intervention);
            if (count($salaries)<$limite){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),50);
                $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                    $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                }
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),100);
                    $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                }
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                    $otdSup = $this->salarieRepository->findOtdSupFinal($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);


                }
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),200);
                    $otdSup = $this->salarieRepository->findOtdSup($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);


                }
                if (count($salarieTotal)<$limite){
                    $coordonnee = $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                    $Otd = $this->fichierOTDRepository->findGlobal($coordonnee[0],$coordonnee[1],$coordonnee[2],$coordonnee[3]);


                }
                if (count($salarieTotal)<$limite){
                    $coordonnee = $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                    $Otd = $this->fichierOTDRepository->findGlobal($coordonnee[0],$coordonnee[1],$coordonnee[2],$coordonnee[3]);

                }

            }
            else{
                $salarieTotal = $salaries;
            }
            $maintenant = $this->definirDate->aujourdhui();
            //$demain = $this->definirDate->duree($maintenant, 'P2D');
            $demain =null;

       /* else{
            $salaries = $this->salarieRepository->findOtdRapide($intervention,$intervention->getRdvAt());
            if (count($salaries)<$limite){
                $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),50);
                $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                if (count($salarieTotal)<3){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),75);
                    $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                }
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),100);
                    $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                }
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),100);
                    $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                }
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),150);
                    $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                }
                if (count($salarieTotal)<$limite){
                    $coordSup =  $this->geoloc->distance($intervention->getAdresse()->getCoordonnees()->getLatitude(),$intervention->getAdresse()->getCoordonnees()->getLongitude(),250);
                    $otdSup = $this->salarieRepository->findOtdSupRapide($intervention,$coordSup[0],$coordSup[1],$coordSup[2],$coordSup[3],$intervention->getRdvAt());
                    $salarieTotal = array_unique(array_merge($salaries,$otdSup),SORT_REGULAR);
                }
            }
            else{
                $salarieTotal = $salaries;
                $Otd = [];
            }
            $maintenant = $this->definirDate->aujourdhui();
            $demain = $this->definirDate->duree($maintenant, 'PT4H');

        }*/

        if (!empty($salarieTotal)){
            foreach ($salarieTotal as $salarie) {
                $proposition = new Proposition();
                $proposition->setSalarie($salarie)
                    ->setInter($intervention)
                    ->setDateFin($demain);
                $this->manager->persist($proposition);
                $adresseMail = $this->mail->getMail($salarie->getId());
                $this->mail->mailDemandeIntervention($adresseMail);
                $this->smsFactor->smsInter($salarie->getTelephone()->getNumero());
            }
        }
        $this->mail->mailDemandeContrainte();
        if (!empty($Otd)){
            foreach ($Otd as $otd) {
                    if ($otd->getMail()){
                        $this->mail->mailOtdHDD($otd->getMail(),$intervention);
                    }


            }
        }



    }




}